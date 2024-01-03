class Modal
{

    // constructor(el)
    // {
    //     this._elHTML = document.documentElement;
    //     this._elBody = document.body;

    // }

    /**
     * Afficher modal au clic sur le bouton filtres 
     */
    afficheModal(elModal)
    {
        elModal.classList.remove('modal--ferme');
        let  elHTML = document.documentElement,
        elBody = document.body;

		elHTML.classList.add("overflow-y-hidden");
		elBody.classList.add("overflow-y-hidden");
        
        elModal.addEventListener('click', this.fermeModal());
        
        this._elFiltresModal.innerHTML =  this._dom;
        this._elFiltresEcran.remove.bind(this);
    }

    /**
     * Fermer le modal au clic sur l'entourage du formulaire de filtrage
     */
    fermeModal(e)
    {
        if(e.target.dataset.jsModal == "exit")
        {
            this._el.classList.add('modal--ferme');
            this._elHTML.classList.remove("overflow-y-hidden");
            this._elBody.classList.remove("overflow-y-hidden");
            
            this._elFiltresEcran.innerHTML =  this._dom;
            this._elFiltresModal.remove.bind(this);
        }
    }
}

export const {afficheModal, fermeModal} = new Modal();