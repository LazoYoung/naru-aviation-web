import MD5 from "crypto-js/md5.js";

export function fetchJSON(url, token) {
    return fetch(url, {
        method: 'GET',
        headers: {'X-CSRF-Token': token}
    }).then(r => r.json());
}

export function fetchResponse(url, token) {
    return fetch(url, {
        method: 'GET',
        headers: {'X-CSRF-Token': token}
    });
}

export function getGravatarHash(email) {
    return MD5(email.trim().toLowerCase());
}
