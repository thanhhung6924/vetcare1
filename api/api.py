import pandas as pd
import mysql.connector
from fastapi import FastAPI, Query
from typing import List
from mlxtend.preprocessing import TransactionEncoder
from mlxtend.frequent_patterns import fpgrowth, association_rules
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
app = FastAPI(title="VetCare AI API - Lambda Architecture")

core_rules_cache = pd.DataFrame()
trending_rules_cache = pd.DataFrame()
# Lưu Top 5 bán chạy nhất
best_sellers_cache = [] 

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
            """
        else:
            query = """
                SELECT o.order_id AS MaDon, p.name AS SanPham
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN products p ON oi.product_id = p.product_id
            """
        df = pd.read_sql(query, conn)
        conn.close()
        return df
    except Exception as e:
        print(f"LỖI DB: {e}")
        return pd.DataFrame()

def generate_rules(df, min_sup, min_conf):
    if df.empty or len(df) < 2: return pd.DataFrame()
    transactions = df.groupby('MaDon')['SanPham'].apply(list).tolist()
    te = TransactionEncoder()
    df_matrix = pd.DataFrame(te.fit_transform(transactions), columns=te.columns_)
    frequent_itemsets = fpgrowth(df_matrix, min_support=min_sup, use_colnames=True)
    if frequent_itemsets.empty: return pd.DataFrame()
    rules = association_rules(frequent_itemsets, metric="confidence", min_threshold=min_conf)
    if not rules.empty:
        rules = rules[rules['lift'] > 1.0] 
    return rules

def reload_core_internal():
    global core_rules_cache, best_sellers_cache
    print("Đang nạp lại Lớp Lõi (Core Rules - Toàn thời gian)...")
    df_all = fetch_data()
    
    # TOP 5 MÓN BÁN CHẠY NHẤT LỊCH SỬ
    if not df_all.empty:
        best_sellers_cache = df_all['SanPham'].value_counts().head(5).index.tolist()
        
    core_rules_cache = generate_rules(df_all, min_sup=0.001, min_conf=0.1)

def reload_trend_internal():
    global trending_rules_cache
    print("Flash Trend")
    df_recent = fetch_data(days_ago=1)
    trending_rules_cache = generate_rules(df_recent, min_sup=0.01, min_conf=0.5)

@app.on_event("startup")
def startup_event():
    reload_core_internal()
    reload_trend_internal()
    print("--- HỆ THỐNG LUẬT SẴN SÀNG ---")

@app.get("/reload-trend")
def reload_trend_only():
    reload_trend_internal()
    return {"message": "Đã cập nhật lại CHỈ luật Trend!"}

@app.get("/reload-core")
def reload_core_only():
    reload_core_internal()
    return {"message": "Đã cập nhật lại CHỈ luật Core và Best Sellers!"}

@app.get("/recommend")
def get_recommendation(cart_items: List[str] = Query(None)):
    global core_rules_cache, trending_rules_cache, best_sellers_cache
    
    # Nếu giỏ hàng trống, ném thẳng Best Sellers ra cho khách lựa
    if not cart_items:
        return {"recommendations": [{"name": item, "confidence": 100, "type": "BÁN CHẠY 🌟"} for item in best_sellers_cache]}

    recommendations = []
    rec_names = set()
    
    def is_relevant(antecedents_set, cart):
        return any(item in antecedents_set for item in cart)

    # QUÉT SP TREND
    if trending_rules_cache is not None and not trending_rules_cache.empty:
        trend_res = trending_rules_cache[trending_rules_cache['antecedents'].apply(lambda x: is_relevant(x, cart_items))]
        if not trend_res.empty:
            top_trend = trend_res.sort_values(by='confidence', ascending=False).head(2)
            for _, row in top_trend.iterrows():
                for item in row['consequents']:
                    if item not in rec_names and item not in cart_items:
                        recommendations.append({
                            "name": item, 
                            "confidence": round(row['confidence'] * 100, 2),
                            "type": "HOT TREND 🔥"
                        })
                        rec_names.add(item)

    # QUÉT SP CORE
    if core_rules_cache is not None and not core_rules_cache.empty and len(recommendations) < 5:
        core_res = core_rules_cache[core_rules_cache['antecedents'].apply(lambda x: is_relevant(x, cart_items))]
        if not core_res.empty:
            top_core = core_res.sort_values(by='confidence', ascending=False).head(5)
            for _, row in top_core.iterrows():
                for item in row['consequents']:
                    if item not in rec_names and item not in cart_items and len(recommendations) < 5:
                        recommendations.append({
                            "name": item, 
                            "confidence": round(row['confidence'] * 100, 2),
                            "type": "THƯỜNG XUYÊN MUA CÙNG"
                        })
                        rec_names.add(item)
                        
    if len(recommendations) < 5:
        for item in best_sellers_cache:
            if item not in rec_names and item not in cart_items and len(recommendations) < 5:
                recommendations.append({
                    "name": item, 
                    "confidence": 99.99, 
                    "type": "SẢN PHẨM BÁN CHẠY 🌟"
                })
                rec_names.add(item)
                        
    return {"recommendations": recommendations}

@app.get("/rules")
def show_all_generated_rules():
    global core_rules_cache, trending_rules_cache, best_sellers_cache
    
    # Hàm phụ để biến DataFrame của mlxtend thành chữ cho dễ đọc
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
                "Độ nâng - Lift": round(row['lift'], 2)
            })
        return formatted_list

    # Lấy danh sách luật ra trước
    trend_list = format_rules_for_display(trending_rules_cache)
    core_list = format_rules_for_display(core_rules_cache)
    
    # Dùng hàm len() để đếm tổng số lượng
    trend_count = len(trend_list)
    core_count = len(core_list)
    best_seller_count = len(best_sellers_cache)

    # In ra JSON với tiêu đề có gắn sẵn số lượng
    return {
        "thong_bao": "ĐÂY LÀ KẾT QUẢ THUẬT TOÁN FP-GROWTH ĐÃ LƯU TRONG RAM",
        f"1_top_ban_chay_nhat (Dự phòng) - Tổng: {best_seller_count} món": best_sellers_cache,
        f"2_luat_ngan_han_TREND (24h qua) - Tổng: {trend_count} luật": trend_list,
        f"3_luat_dai_han_CORE (Toàn thời gian) - Tổng: {core_count} luật": core_list
    }