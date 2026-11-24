import http from "k6/http";
import { check, sleep } from "k6";
import { login } from "./_auth.js";

export const options = {
    vus: 3,
    duration: "30s",
    thresholds: {
        http_req_failed: ["rate<0.05"],
        http_req_duration: ["max<=8000"],
    },
};

const BASE_URL = __ENV.BASE_URL || "http://127.0.0.1:8000";

export default function () {
    if (!login(BASE_URL, "dinkes_administrator", "password123")) {
        return;
    }

    for (const path of [
        "/",
        "/manajemen-logbook",
        "/manajemen-akun",
        "/manajemen-departemen",
        "/manajemen-logbook/kategori-pelayanan-kesehatan",
    ]) {
        const response = http.get(`${BASE_URL}${path}`);
        check(response, {
            [`admin GET ${path} status 200`]: (r) => r.status === 200,
        });
    }

    sleep(1);
}
