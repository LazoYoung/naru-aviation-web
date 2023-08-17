import MD5 from "crypto-js/md5.js";

export function fetchJSON(url, token) {
    return fetch(url, {
        method: 'GET',
        headers: {'X-CSRF-Token': token}
    }).then(r => r.json(), reason => console.error(`Failed to fetch: ${reason}`));
}

export function fetchResponse(url, token) {
    return fetch(url, {
        method: 'GET',
        headers: {'X-CSRF-Token': token}
    }).catch(reason => console.error(`Failed to fetch: ${reason}`));
}

export function getGravatarHash(email) {
    return MD5(email.trim().toLowerCase());
}
