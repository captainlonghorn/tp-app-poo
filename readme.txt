Bonjour,

Afin de faire fonctionner le site tp-app-poo correctement sur ma machine j'ai ajouté ces lignes dans httpd-vhosts.conf :
`<VirtualHost tp-app-poo>
    ServerName tp-app-poo
    DocumentRoot c:/wamp/www/tp-app-poo/Web
    <Directory  "c:/wamp/www/tp-app-poo/Web">
        Options +Indexes +Includes +FollowSymLinks +MultiViews
        AllowOverride All
    </Directory>
</VirtualHost>`

Et ceci dans windows\system32\drivers\etc\host :
`127.0.0.1 tp-app-poo`

Comme précisé dans le cours

--

J'ai créé une classe mère \lib\OCFram AppCache et 2 classes filles pour gérer le cache
Afin de clarifier mon travail, j'ai ajouté une directive SHOW_CACHE_INFO dans la classe mère.
Tant qu'elle est à true, des messages s'afficheront sur les pages pour expliquer ce qui s'est passé au niveau cache.
J'ai également largement commenté Frontend\FrondendApplication Run afin de décrire les évènements.

Les méthodes de delete de cache sont appelées dans les controllers, suite aux updates, etc.

--

