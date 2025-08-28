import whisper
import os
import sys

# Nhận đường dẫn file audio từ tham số dòng lệnh
audio_path = sys.argv[1]

# Load model
model = whisper.load_model("small")

# Transcribe
# result = model.transcribe(audio_path, language="en")
result = model.transcribe(audio_path)

# In ra text
print(result["text"])
