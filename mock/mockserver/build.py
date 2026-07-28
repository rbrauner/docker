#!/usr/bin/env python3
"""Embed physical files as base64 into MockServer expectation configs.

MockServer's declarative JSON cannot read a response body from disk - binary
bodies must be inlined as base64 (`{"type": "BINARY", "base64Bytes": "..."}`).
To still keep a physical file as the source of truth, drop the file under
./files/ and run this script; it re-encodes the file into the target config's
BINARY body. Idempotent - re-run whenever the source file changes.

    python3 mock/mockserver/build-files.py

The mapping lives in ./files-map.json (kept out of ./config so MockServer does
not try to load it as an expectation). One entry per file-serving endpoint:
    "<config path relative to config/>": "<source file relative to files/>"
"""
import base64, json, os

HERE = os.path.dirname(os.path.abspath(__file__))
FILES_DIR = os.path.join(HERE, "files")
CONFIG_DIR = os.path.join(HERE, "config")
MAP_FILE = os.path.join(HERE, "files-map.json")

def embed(config_rel, source_rel):
    src = os.path.join(FILES_DIR, source_rel)
    cfg = os.path.join(CONFIG_DIR, config_rel)
    with open(src, "rb") as f:
        b64 = base64.b64encode(f.read()).decode("ascii")
    with open(cfg) as f:
        data = json.load(f)

    patched = 0
    for expectation in data:
        body = expectation.get("httpResponse", {}).get("body")
        if isinstance(body, dict) and body.get("type") == "BINARY":
            body["base64Bytes"] = b64
            patched += 1
    if patched == 0:
        raise SystemExit(f"no BINARY body found in {config_rel}")

    with open(cfg, "w") as f:
        json.dump(data, f, indent=4)
        f.write("\n")
    print(f"  {source_rel} ({os.path.getsize(src)} B) -> {config_rel} "
          f"[{patched} body, {len(b64)} b64 chars]")

def main():
    with open(MAP_FILE) as f:
        mapping = json.load(f)
    print(f"embedding {len(mapping)} file(s) from files-map.json:")
    for config_rel, source_rel in mapping.items():
        embed(config_rel, source_rel)

if __name__ == "__main__":
    main()
