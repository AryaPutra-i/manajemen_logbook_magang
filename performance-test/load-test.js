import http from "k6/http";
import { check, sleep } from "k6";
import { login } from "./scenarios/_auth.js";

export const options = {
    stages: [
        { duration: "30s", target: 5 },
        { duration: "1m", target: 10 },
        { duration: "30s", target: 0 },
    ],
    thresholds: {
        "http_req_failed{name:logbook}": ["rate<0.05"],
        http_req_duration: ["max<=8000"],
        checks: ["rate>0.95"],
    },
};

const BASE_URL = __ENV.BASE_URL || "http://127.0.0.1:8000";
let isAuthenticated = false;

export default function () {
    const roleIndex = __VU % 5;
    const users = [
        ["dinkes_administrator", "password123"],
        ["admin_unit", "password123"],
        ["dokter.magang", "password123"],
        ["mahasiswa.magang", "password123"],
        ["pembimbing.lapangan", "password123"],
    ];
    const [username, password] = users[roleIndex];

    if (!isAuthenticated) {
        if (!login(BASE_URL, username, password)) {
            return;
        }

        isAuthenticated = true;
    }

    const response = http.get(`${BASE_URL}/manajemen-logbook`, {
        tags: { name: "logbook" },
    });
    check(response, {
        "logbook accessible": (r) => r.status === 200,
        "logbook response within 8 seconds": (r) => r.timings.duration <= 8000,
    });

    sleep(1);
}