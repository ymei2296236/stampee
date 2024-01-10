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
        this.#_el.addEventListener('click', this.#afficheModal.bind(this));
    }

    /**
     * Afficher modal au clic sur le bouton filtres 
     */
         #afficheModal()
         {
            this.#_elDivMenuBar.classList.remove('menu-bar--ferme');
            this.#_elHTML.classList.add("overflow-y-hidden");
            this.#_elBody.classList.add("overflow-y-hidden");
    
            this.#_elDivMenuBar.addEventListener('click', this.#fermeModal.bind(this));
         }
     
         /**
          * Fermer le modal au clic sur l'entourage du formulaire de filtrage
          */
         #fermeModal(e)
         {
             if(e.target.dataset.jsModal !== "image")
             {
                 this.#_elDivMenuBar.classList.add('menu-bar--ferme');
                 this.#_elHTML.classList.remove("overflow-y-hidden");
                 this.#_elBody.classList.remove("overflow-y-hidden");
             }
         }
}