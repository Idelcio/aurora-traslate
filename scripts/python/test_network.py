#!/usr/bin/env python3
"""Test network connectivity from Python when run via PHP."""

import socket
import sys

try:
    # Try to resolve DNS
    print("Testing DNS resolution...")
    ip = socket.gethostbyname('translation.googleapis.com')
    print(f"SUCCESS: Resolved translation.googleapis.com to {ip}")

    # Try to connect
    print("Testing connection...")
    import requests
    response = requests.get('https://www.google.com', timeout=5)
    print(f"SUCCESS: HTTP GET to google.com returned {response.status_code}")

except Exception as e:
    print(f"ERROR: {e}")
    import traceback
    traceback.print_exc()
    sys.exit(1)
