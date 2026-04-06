import pandas as pd
import mysql.connector
import os
import numpy as np
from fastapi import FastAPI, Query
from typing import List
from mlxtend.preprocessing import TransactionEncoder
from mlxtend.frequent_patterns import fpgrowth, association_rules
from collections import defaultdict
from fastapi.responses import PlainTextResponse

app = FastAPI(title="VetCare AI API - Master Hybrid Logic")

core_rules_cache = pd.DataFrame()
trending_rules_cache = pd.DataFrame()
best_sellers_cache = [] 

class FPNode:
    def __init__(self, name, parent):
        self.name = name
        self.count = 1
        self.parent = parent
        self.children = {}

    def display(self, ind=1):
        res = '  ' * ind + f"└── [{self.count}] {self.name}\n"
        for child in self.children.values():
            res += child.display(ind + 1)
        return res

def get_db_connection():
    return mysql.connector.connect(host="localhost", user="root", password="", database="local_kms")

def fetch_data(days_ago=None):
    try:
        conn = get_db_connection()
        if days_ago:
            query = f"""
                SELECT o.order_id AS MaDon, p.name AS SanPham
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN products p ON oi.product_id = p.product_id
                WHERE o.order_date >= DATE_SUB(NOW(), INTERVAL {days_ago} DAY)
                AND o.status = 'completed'
            """
        else:
            query = """
                SELECT o.order_id AS MaDon, p.name AS SanPham
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN products p ON oi.product_id = p.product_id
                WHERE o.status = 'completed'
            """
        df = pd.read_sql(query, conn)
        conn.close()
        return df
    except Exception as e:
        print(f"LỖI DB: {e}")
        return pd.DataFrame()

def generate_rules(df, min_sup, min_conf):
    if df.empty or len(df) < 2: return pd.DataFrame()
    transactions = df.groupby('MaDon')['SanPham'].apply(lambda x: list(set(x))).tolist()
    te = TransactionEncoder()
    df_matrix = pd.DataFrame(te.fit_transform(transactions), columns=te.columns_)
    frequent_itemsets = fpgrowth(df_matrix, min_support=min_sup, use_colnames=True)
    
    if frequent_itemsets.empty: return pd.DataFrame()
    rules = association_rules(frequent_itemsets, metric="confidence", min_threshold=min_conf)
    
    if not rules.empty:
        rules = rules[rules['lift'] >1.0].copy() 
        
        rules = rules[rules['consequents'].apply(lambda x: len(x) == 1)].copy()
        rules['score'] = rules['confidence'] * np.log2(1 + rules['lift'])
        rules = rules.sort_values(by=['score', 'confidence'], ascending=[False, False])
        
    return rules

def export_rules_to_csv():
    global core_rules_cache
    if core_rules_cache is None or core_rules_cache.empty:
        return
        
    try:
        # Lấy thêm cột Score ra file CSV
        export_df = core_rules_cache[['antecedents', 'consequents', 'support', 'confidence', 'lift', 'score']].copy()
        export_df['antecedents'] = export_df['antecedents'].apply(lambda x: ', '.join(list(x)))
        export_df['consequents'] = export_df['consequents'].apply(lambda x: ', '.join(list(x)))
        
        file_path = "vetcare_ai_rules.csv"
        export_df.to_csv(file_path, index=False, encoding='utf-8-sig')
        print(f"✅ Đã tự động lưu thành công file CSV: {file_path}")
    except Exception as e:
        print(f"❌ Lỗi khi lưu CSV: {str(e)}")

def reload_core_internal():
    global core_rules_cache, best_sellers_cache
    print("Đang nạp lại Lớp Lõi (Core Rules - Toàn thời gian)...")
    df_all = fetch_data()
    
    # TOP 5 MÓN BÁN CHẠY NHẤT LỊCH SỬ
    if not df_all.empty:
        best_sellers_cache = df_all['SanPham'].value_counts().head(5).index.tolist()
        
    core_rules_cache = generate_rules(df_all, min_sup=0.001, min_conf=0.274)
    
    # KÍCH HOẠT TỰ ĐỘNG XUẤT CSV
    export_rules_to_csv()

def reload_trend_internal():
    global trending_rules_cache
    print("Đang nạp lại Lớp Flash Trend (24h qua)...")
    df_recent = fetch_data(days_ago=1)
    trending_rules_cache = generate_rules(df_recent, min_sup=0.001, min_conf=0.5)

@app.on_event("startup")
def startup_event():
    reload_core_internal()
    reload_trend_internal()
    print("--- 🚀 HỆ THỐNG AI VETCARE SẴN SÀNG ---")

@app.get("/")
def root_endpoint():
    return {
        "trang_thai": "✅ Server AI VetCare đang hoạt động cực mượt!",
        "huong_dan": "Các Endpoint: /rules (Xem luật), /fp-tree (Xem cây nén), /docs (Tài liệu API)"
    }

@app.get("/reload-trend")
def reload_trend_only():
    reload_trend_internal()
    return {"message": "Đã cập nhật lại CHỈ luật Trend!"}

@app.get("/reload-core")
def reload_core_only():
    reload_core_internal()
    return {"message": "Đã cập nhật lại CHỈ luật Core và Best Sellers, đồng thời xuất CSV!"}

@app.get("/recommend")
def get_recommendation(cart_items: List[str] = Query(None)):
    global core_rules_cache, trending_rules_cache, best_sellers_cache
    
    # NẾU GIỎ TRỐNG -> TRẢ VỀ HÀNG BÁN CHẠY KÈM SCORE 0.0
    if not cart_items:
        return {"recommendations": [{"name": item, "confidence": 99.99, "score": 0.0, "type": "SẢN PHẨM BÁN CHẠY 🌟"} for item in best_sellers_cache]}

    recommendations = []
    rec_names = set()
    
    def is_relevant(antecedents_set, cart):
        return antecedents_set.issubset(set(cart))

    # 1. QUÉT SP TREND 
    if trending_rules_cache is not None and not trending_rules_cache.empty:
        trend_res = trending_rules_cache[trending_rules_cache['antecedents'].apply(lambda x: is_relevant(x, cart_items))]
        if not trend_res.empty:
            top_trend = trend_res.head(4) 
            for _, row in top_trend.iterrows():
                for item in row['consequents']:
                    if item not in rec_names and item not in cart_items:
                        recommendations.append({
                            "name": item, 
                            "confidence": round(row['confidence'] * 100, 2),
                            "score": round(row['score'], 4),
                            "type": "HOT TREND 🔥"
                        })
                        rec_names.add(item)

    # 2. QUÉT SP CORE
    if core_rules_cache is not None and not core_rules_cache.empty and len(recommendations) < 5:
        core_res = core_rules_cache[core_rules_cache['antecedents'].apply(lambda x: is_relevant(x, cart_items))]
        if not core_res.empty:
            # Lấy 5 combo đỉnh nhất
            top_core = core_res.head(5)
            for _, row in top_core.iterrows():
                for item in row['consequents']:
                    if item not in rec_names and item not in cart_items and len(recommendations) < 5:
                        recommendations.append({
                            "name": item, 
                            "confidence": round(row['confidence'] * 100, 2),
                            "score": round(row['score'], 4),
                            "type": "THƯỜNG XUYÊN MUA CÙNG"
                        })
                        rec_names.add(item)
                        
    if len(recommendations) < 5:
        for item in best_sellers_cache:
            if item not in rec_names and item not in cart_items and len(recommendations) < 5:
                recommendations.append({
                    "name": item, 
                    "confidence": 99.99, 
                    "score": 0.0, 
                    "type": "SẢN PHẨM BÁN CHẠY 🌟"
                })
                rec_names.add(item)
                        
    return {"recommendations": recommendations}

@app.get("/rules")
def show_all_generated_rules():
    global core_rules_cache, trending_rules_cache, best_sellers_cache

    def format_rules_for_display(df):
        if df is None or df.empty:
            return []
            
        formatted_list = []
        for _, row in df.iterrows():
            formatted_list.append({
                "Nếu khách xem (Antecedents)": list(row['antecedents']),
                "Gợi ý mua kèm (Consequents)": list(row['consequents']),
                "Độ hỗ trợ - Support": round(row['support'], 4),
                "Độ tin cậy - Confidence": f"{round(row['confidence'] * 100, 2)}%",
                "Độ nâng - Lift": round(row['lift'], 2),
                "Điểm ưu tiên - Score (Hybrid)": round(row['score'], 4)
            })
        return formatted_list

    trend_list = format_rules_for_display(trending_rules_cache)
    core_list = format_rules_for_display(core_rules_cache)
    
    trend_count = len(trend_list)
    core_count = len(core_list)
    best_seller_count = len(best_sellers_cache)

    return {
        "thong_bao": "ĐÂY LÀ KẾT QUẢ FP-GROWTH ĐÃ LỌC SẠCH VÀ SẮP XẾP THEO ĐIỂM SCORE TỐI ƯU",
        f"1_top_ban_chay_nhat (Fallback - Score mặc định 0.0) - Tổng: {best_seller_count} món": best_sellers_cache,
        f"2_luat_ngan_han_TREND (24h qua) - Tổng: {trend_count} luật": trend_list,
        f"3_luat_dai_han_CORE (Toàn thời gian) - Tổng: {core_count} luật": core_list
    }

@app.get("/fp-tree", response_class=PlainTextResponse)
def show_real_fptree():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        cursor.execute("""
            SELECT o.order_id, p.name 
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN products p ON oi.product_id = p.product_id
            WHERE o.status = 'completed'
            ORDER BY o.order_id DESC 
        """)
        rows = cursor.fetchall()
        
        cursor.close()
        conn.close()
        
        transactions_dict = defaultdict(list)
        for row in rows:
            transactions_dict[row['order_id']].append(row['name'])
            
        transactions = [list(set(items)) for items in transactions_dict.values()]
        
        if not transactions:
            return "Không có dữ liệu đơn hàng nào trong Database!"

        item_counts = defaultdict(int)
        for t in transactions:
            for item in t:
                item_counts[item] += 1

        root = FPNode("ROOT (Bắt đầu gốc rễ)", None)
        root.count = len(transactions)

        for t in transactions:
            filtered_t = sorted(t, key=lambda x: item_counts[x], reverse=True)
            
            current = root
            for item in filtered_t:
                if item in current.children:
                    current.children[item].count += 1
                else:
                    current.children[item] = FPNode(item, current)
                current = current.children[item]

        result_text = "=== CÂY NÉN FP-TREE ===\n"
        result_text += f"Tổng số đơn hàng đang quét: {len(transactions)}\n\n"
        result_text += root.display()
        
        return result_text

    except Exception as e:
        return f"Lỗi khi vẽ cây: {str(e)}"