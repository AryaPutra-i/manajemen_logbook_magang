import http from "k6/http";
import { check, sleep } from "k6";
import { extractCsrfToken, login, logout } from "./_auth.js";

export const options = {
    vus: 5,
    duration: "30s",
    thresholds: {
        http_req_failed: ["rate<0.05"],
        http_req_duration: ["max<=8000"],
    },
};

const BASE_URL = __ENV.BASE_URL || "http://127.0.0.1:8000";

export default function () {
    const loginPage = http.get(`${BASE_URL}/login`);
    const token = extractCsrfToken(loginPage.body);

    if (token) {
        const invalidLogin = http.post(
            `${BASE_URL}/login`,
            { _token: token, username: "invalid.user", password: "invalid-password" },
            { redirects: 0 },
        );
        check(invalidLogin, {
            "invalid login rejected": (r) => r.status === 302,
        });
    }

    if (login(BASE_URL, "dokter.magang", "password123")) {
        const home = http.get(`${BASE_URL}/`);
        check(home, {
            "authenticated home accessible": (r) => r.status === 200,
        });

        const logoutResponse = logout(BASE_URL);
        check(logoutResponse, {
            "logout redirects": (r) => r !== null && r.status === 302,
        });
    }

    sleep(1);
}
