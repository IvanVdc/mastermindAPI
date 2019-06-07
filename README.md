# mastermindAPI
Mastermind API to play the game


Con el docker se puede poner en marcha todo el proyecto de forma simple y rápida
docker-compose build
docker-compose up -d

Para pasar los tests:
docker-compose exec php bin/console doctrine:fixtures:load
docker-compose exec php bin/phpunit

Endpoints activos:
GET / 		=> Muestra datos del usuario
POST /new 	=> Crea una partida
POST /play	=> Para hacer una jugada
GET /game	=> Lista de todas las jugadas de la partida actual
