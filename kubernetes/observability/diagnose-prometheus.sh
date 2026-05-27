#!/usr/bin/env bash
# Debug Prometheus connectivity in observability namespace.
set -euo pipefail

NS=observability

echo "=== Pods ==="
kubectl get pods -n "$NS" -l 'app.kubernetes.io/name=prometheus' -o wide
kubectl get pods -n "$NS" -l 'app.kubernetes.io/name=prometheus-node-exporter' -o wide 2>/dev/null || true

echo ""
echo "=== Service ==="
kubectl get svc prometheus-server -n "$NS" -o wide 2>/dev/null || kubectl get svc -n "$NS" | grep -i prom

echo ""
echo "=== Endpoints (must not be <none>) ==="
kubectl get endpoints prometheus-server -n "$NS" 2>/dev/null || true
kubectl get endpointslice -n "$NS" -l kubernetes.io/service-name=prometheus-server 2>/dev/null || true

echo ""
echo "=== Pod logs (last 30 lines) ==="
POD="$(kubectl get pods -n "$NS" -l 'app.kubernetes.io/name=prometheus' -o jsonpath='{.items[0].metadata.name}' 2>/dev/null || true)"
if [[ -n "$POD" ]]; then
  kubectl logs -n "$NS" "$POD" -c prometheus-server --tail=30 2>/dev/null \
    || kubectl logs -n "$NS" "$POD" --tail=30 2>/dev/null || true
  echo ""
  echo "=== Describe pod ==="
  kubectl describe pod -n "$NS" "$POD" | tail -40
else
  echo "No prometheus pod found."
fi

echo ""
echo "=== In-cluster curl (from a debug pod) ==="
PORT="$(kubectl get svc prometheus-server -n "$NS" -o jsonpath='{.spec.ports[0].port}' 2>/dev/null || echo 9090)"
echo "Using service port: ${PORT}"
kubectl run prom-test --rm -i --restart=Never -n "$NS" --image=curlimages/curl -- \
  curl -sS -m 10 "http://prometheus-server.${NS}.svc.cluster.local:${PORT}/api/v1/query?query=up" \
  || echo "curl failed — check Endpoints above are populated and pod is Ready"

echo ""
echo "=== node-exporter targets (job=node-exporter, expect 1+ UP) ==="
kubectl run prom-ne-test --rm -i --restart=Never -n "$NS" --image=curlimages/curl -- \
  curl -sS -m 10 "http://prometheus-server.${NS}.svc.cluster.local:${PORT}/api/v1/query?query=up%7Bjob%3D%22node-exporter%22%7D" \
  2>/dev/null || true

echo ""
echo "=== sample host metrics (need node_network_receive_bytes_total) ==="
kubectl run prom-net-test --rm -i --restart=Never -n "$NS" --image=curlimages/curl -- \
  curl -sS -m 10 "http://prometheus-server.${NS}.svc.cluster.local:${PORT}/api/v1/query?query=count(node_network_receive_bytes_total)" \
  2>/dev/null || true
