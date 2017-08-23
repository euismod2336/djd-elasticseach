# Pre-install
Op linux in elk geval (vermoed daarmee ook Mac) moet de setting `max_map_count` verhoogd worden met

`$ sudo sysctl -w vm.max_map_count=262144`
(zie: https://github.com/docker-library/elasticsearch/issues/111)

Daarna in `.env` een ip-range instellen dat geen conflicten heeft met je interne netwerk (als je op een 192.168.1.1 zit, verander dan de range naar 192.168.2).
Stel je hosts file in op het hierboven ingestelde ipadres en het adres in je `.env` bestand.

Installleer docker (https://www.docker.com/) en docker-compose (https://docs.docker.com/compose/install/)

# Run
In de root van je project

`$ docker-compose up` (dit kaapt je terminal)

Let erop dat de rechten zo staan ingesteld dat de container ook schrijfrechten heeft op de benodigde plekken (in elk geval is dat in de ./conf folder waar de database + elastic search data wordt opgeslagen)
Het kan dus zijn dat je de boel even moet aanpassen en opnieuw opstarten.

# Installatie
## DB
host: `db`<br />
name: `dbname`<br />
user: `Perfectweb Team`<br />
pw: `224fxIgJ`

Installeer met Test data

# Post-setup
De plugin moet nog ontdekt en geactiveerd worden, deze is te vinden in `public_html/plugins/content/djdes`