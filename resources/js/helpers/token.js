import cryptoJS from 'crypto-js';
export function isExpiredToken(token)
{
    if(token==null) return true;

    const time = JSON.parse(cryptoJS.enc.Base64.parse(token.split('.')[1]).toString(cryptoJS.enc.Utf8)).exp;
    
    if(time < new Date().valueOf()/1000)
    {
        return true;
    }

    return false;
}