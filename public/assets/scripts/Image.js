export default class Image
{
    constructor(el)
    {
        this._el = el;
        this._elHTML = document.documentElement;
        this._elBody = document.body;

        this._elsThumbnail = this._el.querySelectorAll('[data-js-thumbnail]');
        this._elImagePrincipale = this._el.querySelector('[data-js-image]');
        this._elModal = document.querySelector('[data-js-modal="exit"]');
        this._elModalImage = this._elModal.querySelector('[data-js-modal="image"]');

        this.init();
    }

    init()
    {
        // Gestion d'evenement au clic sur une thumbnail d'image
        for (let i = 0, l = this._elsThumbnail.length; i < l; i++) 
        {  
            this._elsThumbnail[i].addEventListener('click', this.afficherImage.bind(this),true);
        }
        // this._elImagePrincipale.addEventListener('click', this.gereTaille.bind(this));
        this._elImagePrincipale.addEventListener('click', this.afficheModal.bind(this));


    }

    
    /**
     * Afficher image
     * @param {*} evenement 
     */
    afficherImage(e)
    {
        if (e.currentTarget.dataset.jsThumbnail)
        {
            let nomImage = e.currentTarget.dataset.jsThumbnail,
                dom = `
                        <img src="http://localhost:8888/stampee/public/assets/img/jpg/${nomImage}" alt="image du timbre en recto">
                    `;

            this._elImagePrincipale.innerHTML = dom;

            this.gereActive(e.currentTarget);
        }
    }


    /**
     *  Gerer le style d'image choisie
     * @param {*} ElementHTML 
     */
    gereActive(imageActive)
    {
        let styleActive = this._el.querySelector('.images__thumbnails-item--active');

        if (styleActive) styleActive.classList.remove('images__thumbnails-item--active');

        imageActive.classList.add('images__thumbnails-item--active');
    }

    // gereTaille(e)
    // {
    //     let width = this._elImage.clientWidth;
    //     let height = this._elImage.clientHeight;

    //     if (e.target.dataset.jsBtn == 'ZoomIn') 
    //     {

    //         this._elImage.style.width = (width + 50) + "px";
    //         this._elImage.style.height = (height + 50) + "px";

    //     }
    //     if (e.target.dataset.jsBtn == 'ZoomOut') 
    //     {
    //         this._elImage.style.width = (width - 50) + "px";
    //         this._elImage.style.height = (height - 50) + "px";
    //     }
    // }

     /**
     * Afficher modal au clic sur le bouton filtres 
     */
     afficheModal()
     {
        this._elModal.classList.remove('modal--ferme');
        this._elHTML.classList.add("overflow-y-hidden");
        this._elBody.classList.add("overflow-y-hidden");

        let elImage = this._elImagePrincipale.querySelector('img'),
            srcImage = elImage.getAttribute('src');

        this._elModalImage.setAttribute('src', srcImage);

        this._elModal.addEventListener('click', this.fermeModal.bind(this));

     }
 
     /**
      * Fermer le modal au clic sur l'entourage du formulaire de filtrage
      */
     fermeModal(e)
     {
         if(e.target.dataset.jsModal !== "image")
         {
             this._elModal.classList.add('modal--ferme');
             this._elHTML.classList.remove("overflow-y-hidden");
             this._elBody.classList.remove("overflow-y-hidden");
         }
     }



}