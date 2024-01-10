
export default class Filtrage
{
    #_el;
    #_elHTML;
    #_elBody;
    #_elModal;
    #_elFiltresEcran;
    #_elFiltresModal;
    #_dom;

    /**
     * Gestion du modal (version tablette)
     * @param {*} el 
     */
    constructor(el)
    {
        this.#_el = el;
        this.#_elHTML = document.documentElement;
        this.#_elBody = document.body;
        this.#_elModal = document.querySelector('[data-js-modal]');
        this.#_elFiltresEcran = document.querySelector('[data-js-filtres="ecran"]');
        this.#_elFiltresModal = document.querySelector('[data-js-filtres="modal"]');
        this.#_dom = this.#_elFiltresEcran.innerHTML;

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
        this.#_elModal.classList.remove('modal--ferme');
		this.#_elHTML.classList.add("overflow-y-hidden");
		this.#_elBody.classList.add("overflow-y-hidden");
        
        this.#_elModal.addEventListener('click', this.#fermeModal.bind(this));
        this.#_elFiltresModal.innerHTML =  this.#_dom;
        this.#_elFiltresEcran.remove.bind(this);
    }

    /**
     * Fermer le modal au clic sur l'entourage du formulaire de filtrage
     */
    #fermeModal(e)
    {
        if(e.target.dataset.jsModal == "exit")
        {
            this.#_elModal.classList.add('modal--ferme');
            this.#_elHTML.classList.remove("overflow-y-hidden");
            this.#_elBody.classList.remove("overflow-y-hidden");
            
            this.#_elFiltresEcran.innerHTML =  this._dom;
            this.#_elFiltresModal.remove.bind(this);
        }
    }
}