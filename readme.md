## Pre-install
Op linux in elk geval (vermoed daarmee ook Mac) moet de setting `max_map_count` verhoogd worden met

`$ sudo sysctl -w vm.max_map_count=262144`
(zie: https://github.com/docker-library/elasticsearch/issues/111)

Daarna in `.env` een ip-range instellen dat geen conflicten heeft met je interne netwerk (als je op een 192.168.1.1 zit, verander dan de range naar 192.168.2).
Stel je hosts file in op het hierboven ingestelde ipadres en het adres in je `.env` bestand.

Installleer docker (https://www.docker.com/) en docker-compose (https://docs.docker.com/compose/install/)

## Run
In de root van je project

`$ docker-compose up` (dit kaapt je terminal)

Let erop dat de rechten zo staan ingesteld dat de container ook schrijfrechten heeft op de benodigde plekken (in elk geval is dat in de ./conf folder waar de database + elastic search data wordt opgeslagen)
Het kan dus zijn dat je de boel even moet aanpassen en opnieuw opstarten.

## Installatie
### DB
host: `db`<br />
name: `dbname`<br />
user: `Perfectweb Team`<br />
pw: `224fxIgJ`

Installeer met Test data indien gewenst. Verwijder de installatie folder na installatie. Als er geen `configuration.php` bestand aangemaakt is, kopieer die handmatig in de root en vul de correcte gegevens in.

## Post-setup
De plugin moet nog ontdekt en geactiveerd worden, deze is te vinden in `public_html/plugins/content/djdes`

Na het opslaan van een artikel is die opgeslagen in de ElasticSearch.

Als de content groep van plugins is ingeladen, kan met het event `onGetFromElasticSearch` in ElasticSearch gezocht worden (zie de plugin voor details)

Daarnaast kan je handmatig naar het IP-adres gaan van de server (default: 192.168.13.8:9200) en daar via een GET de data bekijken (nadat een artikel is opgeslagen zou dat zijn: 192.168.13.8:9200/djd/_search). Ook kan je direct naar het opgeslagen artikel gaan door de ID ook in de url op te nemen.
De opbouw van de urls is: {host}/{index}/{type}/{id|keyword}

## HTPasswd
ElasticSearch is beveiligd met een http authentication (deze staat standaard ingevuld in de plugin):

user: `elastic`<br />
pw: `changeme`