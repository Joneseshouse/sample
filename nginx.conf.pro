server {
    listen 443;
    server_name my.domain;
    charset utf-8;
    root /code/public;
    index index.php index.html;

    ssl on;
    # ssl_certificate /etc/letsencrypt/live/my.domain/fullchain.pem;
    # ssl_certificate_key /etc/letsencrypt/live/my.domain/privkey.pem;

    ssl_certificate /resource/ssl/24horder/fullchain.pem;
    ssl_certificate_key /resource/ssl/24horder/privkey.pem;

    location / {
        try_files $uri /index.php?$args;
    }

    location /api/v1/ {
        try_files $uri /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 24horder_api:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location /public {
        try_files $uri $uri/ /index.html;
        rewrite ^/public(/.*)$ $1 break;
        add_header Access-Control-Allow-Origin *;
    }

    location /admin {
        root /resource/public/24horder1/clients;
        try_files $uri $uri/ /admin/index.html =404;
        add_header Access-Control-Allow-Origin *;
    }
    location /user {
        root /resource/public/24horder1/clients;
        try_files $uri $uri/ /user/index.html =404;
        add_header Access-Control-Allow-Origin *;
    }
}
