import BaseApi from '../base';
class TokenApi extends BaseApi
{
    /**
     * 
     * @param {string} username 
     * @param {string} password 
     * @param {string} [f2aCode] 
     */
    createToken(username, password, f2a_code = null)
    {
        this.setUrl('/auth/login');

        this.setData({
            username,
            password,
            f2a_code
        });

        return this.send('post');
    }

    refreshToken(refresh_token)
    {
        this.setUrl('/auth/refresh');

        this.setData({
            refresh_token,
        });

        return this.send('post');
    }
    deleteToken(access_token)
    {
        this.setUrl('/auth/token');

        this.setData({
            access_token,
        });

        return this.send('delete');
    }

     /**
     * 
     * @param {string} code 
     * @param {string} email 
     */
    resetPassword (code, password, email) {
        this.setUrl('auth/reset');

        this.setData({
            code,
            password,
            email
        });

        return this.send('post');
    }
}

export default TokenApi;
