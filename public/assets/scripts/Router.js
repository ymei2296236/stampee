export default class Router 
{
    #_elSelectTri;

    constructor() 
    {
        this.#_elSelectTri = document.querySelector('[data-js-component="TrierEncheres"]');

        this.#init();
    }

    #init() 
    {        
        /**
        * À l'événement change du select, change la route d'Url selon l'option selectionnee
        */
        this.#_elSelectTri.addEventListener('change', function(e) 
        {   
            let elOption = this.#_elSelectTri.querySelector('option:checked'),
                valueOption = elOption.value;
            
            location = `${valueOption}`;

        }.bind(this));
        
        this.#gereSelectTri();
    }

    /**
     * Ajouter l'attribut 'selected' à l'option selectionnee
     */
    #gereSelectTri()
    {
        let href = location.href,
            indexSlug = href.lastIndexOf('/') + 1,
            slug = href.slice(indexSlug),
            elsOption = this.#_elSelectTri.querySelectorAll("option");

        for (let i = 0, l = elsOption.length; i < l; i++) 
        {
            if(elsOption[i].value == slug) elsOption[i].selected = true;
        }
    }
}