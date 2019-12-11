/* 
questo è uno scraper semplice da Wikipedia che ho scaricato da internet ()
https://www.freecodecamp.org/news/the-ultimate-guide-to-web-scraping-with-node-js-daa2027dcd3/
per farlo funzinonare ho dovuto installare:
- node.js
- modulo request
- modulo request-promise
- modulo fs

per farlo partire lo lancio da terminale usando node
ho modificato il programma così che l'output viene scritto su un file separato e non lanciato direttamente sul desktop

il programma ancora non funziona per una questione di accesso: node non ha accesso per scrivere una nuova cartella sul mio computer.
da risolvere


*/


const rp = require('request-promise');
const url = 'https://en.wikipedia.org/wiki/List_of_Presidents_of_the_United_States';
const fs = require('fs');
var data = [];

data = rp(url)
  .then(function(html){
    //success!
    //console.log(html);
  })
  .catch(function(err){
    //handle error
  });

fs.writeFile("/test", data, function(err) {

    if(err) {
        return console.log(err);
    }

    console.log("The file was saved!");
}); 



