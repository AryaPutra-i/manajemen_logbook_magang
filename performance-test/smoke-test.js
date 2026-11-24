import http from "k6/http";
import { check, sleep } from "k6";

export const options = {
    vus: 1,
    iterations: 10,
    thresholds: {
        http_req_failed: ["rate<0.01"],
        http_req_duration: ["max<=8000"],
    },
};

const BASE_URL = __ENV.BASE_URL || "http://127.0.0.1:8000";

export default function () {
    const response = http.get(`${BASE_URL}/up`);

    check(response, {
        "status code 200": (r) => r.status === 200,
        "response time <= 8 seconds": (r) => r.timings.duration <= 8000,
    });

    sleep(1);
}
