export default (str: string) =>
    crypto.subtle
        .digest('SHA-512', new TextEncoder().encode(str))
        .then(buf => Array.prototype.map.call(
                new Uint8Array(buf), x => (
                    ('00' + x.toString(16)).slice(-2)
                ),
            ).join(''),
        );
