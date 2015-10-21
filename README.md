# Token Auth (WIP)
A simple token authentication / custom JSON api enabling plugin. 

### Setup
```public/index.php``` needs
```
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization");
```
at the top. ```// TODO: make it so only API files need this```

Apache users also need to add the following to their ```public/.htaccess``` file
```
RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```
because Apache currently removes the header if it's not base46 encoded username:password.

### Template Tags
```{% requireJwt %}```
Template requires a valid JWT token (sent as the Authorization header ```Authorization: Bearer [jwt]```) to access / use.

```{% tokenAuthLogin %}```
Turn template into a login point. POSTing a ```loginName``` and ```password``` will return a JWT, or error if one occurs.
Attempting to access the template via non-POST means will result in a 400 error.
Ideally you'll want your login route SSL'd as (at the moment) you have to send the password as plain text.

```{% tokenAuthPost %}```
Catch-all POST request handler. Routes requests according to their specified action (```act``` in the post request).
Will throw a 400 error if not accessed via POST. Requires a valid JWT in the Authorization header.
You can use the same forms as you would on the front-end of a Craft site, except you'll need to switch out the ```action``` input for ```act``.

```{% returnJson %}```
Sets the Content-Type header to application/json.


### Example Get API
```twig
{% requireJwt %}
{% returnJson %}
{% set data = {'tokenValid':'true'} %}
{{ data|json_encode()|raw }}
```

### TODO
- [ ] Make it just generally less shit.
- [ ] Test with CORS / CSRF (currently only tested with CSRF disabled)