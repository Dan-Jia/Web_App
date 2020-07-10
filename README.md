# A web application for data visualisation

## Process
1. Cleaning data
2. Uploading data to Database (Here uses Elasticsearch as the database)
3. Retrieving and visualizing data on the web (through web projects developed by PHP & AJAX + web environment built by MAMP)

## Usage
1. Clone the repository
```markdown
  git clone https://github.com/shellswestern/Web_App.git
```
2. Download MAMP; Elasticsearch, Kibana (same version)

3. Move documents of the repository under /Applications/MAMP/htdocs folder

4. Run MAMP, Elasticsearch, Kibana (in order)

5. Use this web app to:
>- [Upload Data](http://localhost:8888/WebinterfaceDataMgt.php)
>- [Visualize Data](http://localhost:8888/WebinterfaceVisFilter.php)
>- [Check the cluster health status](http://localhost:9200/_cat/indices?v)

