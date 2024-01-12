export default class TrierEncheres
{
    #_el;
    #_elSelect;
    #_requete;
    #_oOptions;
    #_elListe;
    #_elFiltresEcran;
    #_elsCheckbox;
    #_elResultatFiltre;
    #_urlRacine;

    constructor(el)
    {
        this.#_el = el;
        this.#_elSelect = this.#_el.querySelector('select');
        this.#_requete = new Request('/stampee/App/requetesFetch.php');
        this.#_oOptions = 
        { 
            method: 'POST',
            headers: { "Content-Type": "application/json" }
        };
        this.#_elListe = document.querySelector('[data-js-listeEncheres]');
        this.#_elFiltresEcran = document.querySelector('[data-js-filtres="ecran"]');
        this.#_elsCheckbox = this.#_elFiltresEcran.querySelectorAll('input[type=checkbox]');
        this.#_elResultatFiltre = document.querySelector('[data-js-resultat]');
        this.#_urlRacine = 'http://localhost:8888/stampee/public/';
        // this.#_urlRacine = 'https://e2296236.webdev.cmaisonneuve.qc.ca/stampee/public/';

        this.#init();
    }
    
    #init()
    {
        this.#_elSelect.addEventListener('change', function()
        {
            // supprimer les filtres selectiones
            this.supprimeFiltre();

            // faire appel asynchrone pour recuperer des donness
            let selectValue = this.#_elSelect.value;

            this.#_oOptions.body = JSON.stringify({action:selectValue});

            this.#appelFetch()
                .then(function(data){
                    // console.log(data);
                    
                    let dom ='';

                    for (let i = 0, l = data.length; i < l; i++) 
                    {   
                        dom += 
                            `
                                <div class="item">
                                <a href="${this.#_urlRacine}enchere/show/${data[i]['enchere_id']}" class="item__lien">
                            `;

                        if(data[i]['coup_de_coeur'] == 1)
                        {
                            dom += 
                                `
                                    <div class="item__coups-de-coeur item__coups-de-coeur--yes">
                                        <i class="fa-solid fa-heart"></i>
                                        <p class="note">Coups de coeur du Lord</p>
                                    </div>
                                `;
                        }
                        else
                        {
                            dom +=
                                `               
                                    <div class="item__coups-de-coeur"></div>
                                `;
                        }
                        dom += 
                            `
                                <div class="item__contenu" data-js-tri>
                                    <div class="item__img">
                                        <img src="${this.#_urlRacine}assets/img/jpg/${data[i]['image']}" alt="Image du timbre">
                                    </div>
                                    <p class="item__description">${data[i]['timbre_nom']} ${data[i]['timbre_nom_2']}</p>
                            `;

                        if (data[i]['archive'] == true)
                        {
                            dom += 
                                `
                                    <div class="prix prix--archive">
                                        <p class="prix__description prix__description--archive">Mise finale</p>
                                        <p class="prix__montant prix__montant--archive">${data[i]['offre']}<small class="prix_decimal"> $</small></p>
                                    </div>
                                    <button class="btn btn--icon-text btn--disabled ">
                                    <i class="fa-solid fa-box-archive"></i>Enchère terminée
                                    </button>
                                `;
                        }
                        else
                        {
                            dom += 
                                `
                                    <div class="prix">
                                        <p class="prix__description">Mise courante</p>
                                        <p class="prix__montant">${data[i]['offre']}<small class="prix_decimal"> $</small></p>
                                    </div>
                                    <span class="btn btn--principal item__btn">Miser</span>
                                `;
                        }

                        dom +=
                                ` 
                                    </div>
                                        </a>
                                    </div>
                                `;
                                
                        this.#_elListe.innerHTML = dom;
                    }
                }.bind(this))
                .catch (function(err) {
                    console.log(`Il y a eu un problème avec l'opération fetch: ${err.message}`);
                });
        }.bind(this))

        
    }

    /**
     * Retourne la promesse de la fonction asynchrone
     * @returns 
     */
        async #appelFetch() {
            try {
                let response = await fetch(this.#_requete, this.#_oOptions);
                if (response.ok) 
                {
                    // console.log(response);
                    return response.json();
                }
                else throw new Error('La réponse n\'est pas OK');
    
            } catch (err) {
                return err.message;
            }
        };
    
    supprimeFiltre()
    {
        // supprimer le resultat de filtrage
        this.#_elResultatFiltre.innerHTML = '';

        // enlever les filtres selectionnes
        for (let i = 0, l = this.#_elsCheckbox.length; i < l; i++) 
        {
            if (this.#_elsCheckbox[i].checked) 
            {
                this.#_elsCheckbox[i].checked = false;
            }
        }



    }


}