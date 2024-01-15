export default class MenuBar
{
    #_el;
    #_elHTML;
    #_elBody;
    #_elDivMenuBar;
    #_elMenuBar;

    constructor(el)
    {
        this.#_el = el;
        this.#_elHTML = document.documentElement;
        this.#_elBody = document.body;
        this.#_elDivMenuBar = document.querySelector('[data-js-menu-bar="exit"]');
        this.#_elMenuBar = this.#_elDivMenuBar.querySelector('[data-js-menu]');

        this.#init();
    }


    #init()
    {
        this.#_el.addEventListener('click', this.#afficheMenuBar.bind(this));
    }

    /**
     * Afficher modal au clic sur le bouton filtres 
     */
         #afficheMenuBar()
         {
            this.#_elDivMenuBar.classList.remove('menu-bar--ferme');
            this.#_elHTML.classList.add("overflow-y-hidden");
            this.#_elBody.classList.add("overflow-y-hidden");
    
            this.#_elDivMenuBar.addEventListener('click', this.#fermeMenuBar.bind(this));
         }
     
         /**
          * Fermer le modal au clic sur l'entourage du formulaire de filtrage
          */
         #fermeMenuBar(e)
         {
             console.log(e.target);
             if(e.target.dataset.jsMenuBar == "exit")
             {
                 this.#_elDivMenuBar.classList.add('menu-bar--ferme');
                 this.#_elHTML.classList.remove("overflow-y-hidden");
                 this.#_elBody.classList.remove("overflow-y-hidden");
             }
         }
}