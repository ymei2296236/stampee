import TrierEncheres from "./TrierEncheres.js";
import Image from "./Image.js";
import Filtrage from "./Filtrage.js";
import MenuBar from "./MenuBar.js";
import Router from "./Router.js";

export const classMapping =
{
    // 'TrierEncheres': TrierEncheres, // pour appels asynchrones - javascript
    'Router': Router, // pour appels synchrones - php 
    'Image': Image,
    'Filtrage': Filtrage,
    'MenuBar': MenuBar
}