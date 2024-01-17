export default class Image
{
    #_el;
    #_elHTML;
    #_elBody;
    #_elsThumbnail;
    #_elImage;
    #_elModal;
    #_elModalImage;
    #_urlRacine;

    constructor(el)
    {
        this.#_el = el;
        this.#_elHTML = document.documentElement;
        this.#_elBody = document.body;

        this.#_elsThumbnail = this.#_el.querySelectorAll('[data-js-thumbnail]');
        this.#_elImage = this.#_el.querySelector('[data-js-image]');
        this.#_elModal = document.querySelector('[data-js-modal="exit"]');
        this.#_elModalImage = this.#_elModal.querySelector('[data-js-modal="image"]');
        this.#_urlRacine = 'http://localhost:8888/stampee/public/';
        // this.#_urlRacine = 'https://e2296236.webdev.cmaisonneuve.qc.ca/stampee/public/';

        this.#init();
    }

    #init()
    {
        // Gestion d'evenement au clic sur une thumbnail d'image
        for (let i = 0, l = this.#_elsThumbnail.length; i < l; i++) 
        {  
            this.#_elsThumbnail[i].addEventListener('click', this.#afficherImage.bind(this),true);
        }
        this.#_elImage.addEventListener('click', this.#afficheModal.bind(this));
    }

    
    /**
     * Afficher image 
     * @param {*} evenement 
     */
    #afficherImage(e)
    {
        // Gerer le style de thumbnail
        let thumbnailActive = this.#_el.querySelector('.images__thumbnails-item--active');
        
        if (thumbnailActive) thumbnailActive.classList.remove('images__thumbnails-item--active');
        
        e.target.classList.add('images__thumbnails-item--active');

        // Creer et inserer le dom de l'image 
        let nomImage = e.currentTarget.dataset.jsThumbnail,
            dom = `
                    <img src="${this.#_urlRacine}assets/img/jpg/${nomImage}" alt="image du timbre en recto">
                `;

        this.#_elImage.innerHTML = dom;
    }


     /**
     * Afficher modal au clic sur le bouton filtres 
     */
     #afficheModal()
     {
        this.#_elModal.classList.remove('modal--ferme');
        this.#_elHTML.classList.add("overflow-y-hidden");
        this.#_elBody.classList.add("overflow-y-hidden");

        let elImage = this.#_elImage.querySelector('img'),
            srcImage = elImage.getAttribute('src');

        this.#_elModalImage.setAttribute('src', srcImage);

        this.#_elModal.addEventListener('click', this.#fermeModal.bind(this));
     }
 
     /**
      * Fermer le modal au clic sur l'entourage du formulaire de filtrage
      */
     #fermeModal(e)
     {
         if(e.target.dataset.jsModal !== "image")
         {
             this.#_elModal.classList.add('modal--ferme');
             this.#_elHTML.classList.remove("overflow-y-hidden");
             this.#_elBody.classList.remove("overflow-y-hidden");
             
             this.#_elModalImage.setAttribute('src', '');
         }
     }



}