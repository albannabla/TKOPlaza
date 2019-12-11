/* 
questo è uno scraper semplice da Wikipedia che ho scaricato da internet ()
https://www.freecodecamp.org/news/the-ultimate-guide-to-web-scraping-with-node-js-daa2027dcd3/
per farlo funzinonare ho dovuto installare:
- node.js
- modulo request
- modulo request-promise

per farlo partire lo lancio da terminale usando node

l'output dello script viene mandato direttamente su terminale
*/


const rp = require('request-promise');
const url = 'https://en.wikipedia.org/wiki/List_of_Presidents_of_the_United_States';

rp(url)
  .then(function(html){
    //success!
    console.log(html);
  })
  .catch(function(err){
    //handle error
  });