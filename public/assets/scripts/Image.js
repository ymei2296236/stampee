export default class Image
{
    constructor(el)
    {
        this._el = el;
        this._elsThumbnail = this._el.querySelectorAll('[data-js-thumbnail]');
        this._image = this._el.querySelector('[data-js-image]');
        this.init();
    }

    init()
    {
        // Gestion d'evenement au clic sur une thumbnail d'image
        for (let i = 0, l = this._elsThumbnail.length; i < l; i++) 
        {  
            this._elsThumbnail[i].addEventListener('click', this.afficherImage.bind(this),true);
        }
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

            this._image.innerHTML = dom;

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

}