const openpgp = require('openpgp');
const fs = require('fs');
const path = require('path');    
const filePath = path.join(__dirname, process.argv[2]);

const signData = new Promise(function (resolve, reject) {
    const passphrase = process.argv[3]; // what the private key is encrypted with
    fs.readFile(filePath, 'utf8', async function (err, data) {
        if (err) {
            reject(err);
        }
        const privateKeyArmored = data; // encrypted private key
        const { keys: [privateKey] } = await openpgp.key.readArmored(privateKeyArmored);
        await privateKey.decrypt(passphrase);

        const { data: text } = await openpgp.sign({
            message: openpgp.cleartext.fromText(process.argv[4]), // CleartextMessage or Message object
            privateKeys: [privateKey]                             // for signing
        });
        resolve(text);
    });
});
signData.then((data) => {
    data = data.split("\r\n").join("\\n")
    process.stdout.write(data);
})