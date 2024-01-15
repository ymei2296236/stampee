<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Catalogue des enchères de timbres de tous prix de tous pays de différentes dimensions">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{url_racine}}assets/css/styles.css"  media="screen">

    <script type="module" src="{{url_racine}}assets/scripts/main.js" defer ></script>

    <title>{% block title %}{% endblock %}</title>
</head>

<body>
    <header>
        <section class="menu-bar menu-bar--ferme" data-js-menu-bar="exit">
            <div data-js-menu class="menu-bar__item">
                    <a href="#" class="lien">English</a>
                    {% if guest %}
                    <a href="{{url_racine}}usager/login" aria-label="Se connecter" class="btn btn--secondaire">Se connecter</a>
                    <a href="{{url_racine}}usager/create" aria-label="Devenir membre" class="btn btn--principal">Devenir membre</a>
                    {% else %}
                    <span href="{{url_racine}}profil/index" class="profil">{% if session.user_id %}{{session.user_id}} {% endif %}</span>
                    <a href="{{url_racine}}profil/index" aria-label="Votre compte" class="btn btn--principal">Votre compte</a>
                    <a href="{{url_racine}}usager/logout" aria-label="Se déconnecter" class="btn btn--secondaire">Se déconnecter</a>
                    {% endif %}
                </div>
        </section>

        <section class="top-bar">
            <span class="logo"><a href="{{url_racine}}"><img src="{{url_racine}}assets/img/svg/logo.svg" alt="logo du site"></a></span>
            <div>
                <a href="#" class="lien">English</a>
                {% if guest %}

                <a href="{{url_racine}}usager/login" aria-label="Se connecter" class="btn btn--secondaire">Se connecter</a>
                <a href="{{url_racine}}usager/create" aria-label="Devenir membre" class="btn btn--principal">Devenir membre</a>
                {% else %}
                <span href="{{url_racine}}profil/index" class="profil">{% if session.user_id %}{{session.user_id}} {% endif %}</span>
                <a href="{{url_racine}}profil/index" aria-label="Votre compte" class="btn btn--principal">Votre compte</a>
                <a href="{{url_racine}}usager/logout" aria-label="Se déconnecter" class="btn btn--secondaire">Se déconnecter</a>
                {% endif %}
            </div>
        </section>
        <section class="navigation">
            <nav>
                <ul>
                    <li><a href="{{url_racine}}">Accueil</a></li>
                    <li><a href="{{url_racine}}enchere/index">Catalogue</a></li>
                </ul>
                <span data-js-component="MenuBar" class="menu-mobile"><i class="fa-solid fa-bars fa-lg"></i></span>
            </nav>
            <div class="navigation__recherche">
                <form class="navigation__form" action="{{url_racine}}enchere/rechercher" method="GET">
                    <label for="rechercher"></label>
                    <input class="navigation__input" type="text" placeholder="Rechercher" id="rechercher" name="motDeCle">
                    <button  aria-label="Rechercher" type="submit" class="btn btn--principal btn--icon"><i class="icon-recherche fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

        </section>
    </header>
    {% block body %}

    {% endblock %}
    <footer>
        <nav>
            <ul>
                <li><a href="#">ACTUALITÉS</a></li>
                <li><a href="#">TERMES ET CONDITIONS</a></li>
                <li><a href="#">NOUS JOINDRE</a></li>
                <li><a href="#">AIDE</a></li>
            </ul>
        </nav>
        <div>
            <p>Droits d’auteur © Stampee Inc. 2023, tous droits réservés</p>
        </div>
    </footer>
</body>
</html>
