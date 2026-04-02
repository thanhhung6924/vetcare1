@echo off
title VetCare AI Server (Port 8000)
echo Dang khoi dong He thong AI...
uvicorn api:app --reload --host 127.0.0.1 --port 8000
pause