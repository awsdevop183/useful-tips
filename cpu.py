#!/usr/bin/python3
import psutil
import urllib, requests
# Get CPU usage as a percentage
cpu_percent = psutil.cpu_percent(2)

# Get virtual memory usage
mem = psutil.virtual_memory()

# Get memory usage statistics
mem_total = mem.total/(1024*1024) # Convert to MB
mem_available = mem.available/(1024*1024) # Convert to MB
mem_percent = mem.percent

if mem_percent > 75 and cpu_percent > 50:
    message = f"Server: madhukarreddy.com \n CPU usage: {cpu_percent}%,\nMemory usage: {mem_available:.2f}/{mem_total:.2f} MB ({mem_percent}%)"
    url = 'https://api.telegram.org/bot%s/sendMessage?chat_id=%s&text=%s' % (
    '6028218922:AAEIYD4RhPm7Udh37CzR02EdPxiwqVARTko', '1650535147', urllib.parse.quote_plus(message))
    _ = requests.get(url, timeout=10)

# Print the results
print(f"CPU usage: {cpu_percent}%")
print(f"Memory usage: {mem_available:.2f}/{mem_total:.2f} MB ({mem_percent}%)")
