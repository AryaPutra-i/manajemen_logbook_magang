import http from "k6/http";
import { check } from "k6";

export function extractCsrfToken(html) {
    const match = html.match(/name="_token"[^>]*value="([^"]+)"/);
    return match ? match[1] : null;
}

export function login(baseUrl, username, password) {
    const loginPage = http.get(`${baseUrl}/login`, {
        tags: { name: "login_page" },
    });

    const pageLoaded = check(loginPage, {
        "login page status 200": (response) => response.status === 200,
    });
    if (!pageLoaded) {
        return false;
    }

    const token = extractCsrfToken(loginPage.body);
    if (!token) {
        console.error("CSRF token tidak ditemukan pada halaman login");
        return false;
    }

    const response = http.post(
        `${baseUrl}/login`,
        {
            _token: token,
            username: username,
            password: password,
        },
        { redirects: 0, tags: { name: "login_submit" } },
    );

    return check(response, {
        "login redirect 302": (r) => r.status === 302,
        "login redirect home": (r) => {
            const location = r.headers.Location || "";
            const path = location
                .replace(/^https?:\/\/[^/]+/, "")
                .split(/[?#]/, 1)[0];
            return path === "" || path === "/";
        },
    });
}

export function logout(baseUrl) {
    const homePage = http.get(`${baseUrl}/`);
    const token = extractCsrfToken(homePage.body);

    if (!token) {
        return null;
    }

    return http.post(
        `${baseUrl}/logout`,
        { _token: token },
        { redirects: 0 },
    );
}
