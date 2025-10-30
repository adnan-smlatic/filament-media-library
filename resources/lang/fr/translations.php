<?php

return [
    'file' => 'fichier',
    'meta' => 'description',
    'author' => 'auteur',
    'image' => 'image',
    'images' => 'images',
    'information' => 'informations',
    'edit-media' => 'éditer',
    'edit-media-description' => 'enregistrer des informations supplémentaires pour cet élément multimédia.',
    'move-media' => 'déplacer le(s) média(s) vers',
    'move-media-description' => 'Actuellement dans :name',
    'dimensions' => 'dimensions',
    'description' => 'description',
    'type' => 'type',
    'caption' => 'légende',
    'alt-text' => 'texte alternatif',
    'actions' => 'actions',
    'size' => 'taille',
    'page' => 'page|pages',
    'duration' => 'durée',
    'root-folder' => 'dossier racine',

    'time' => [
        'created_at' => 'créé le',
        'updated_at' => 'modifié le',
        'published_at' => 'publié le',
        'uploaded_at' => 'téléchargé le',
        'uploaded_by' => 'téléchargé par',
    ],

    'phrases' => [
        'select' => 'sélectionner',
        'select-image' => 'sélectionner une image',
        'no' => 'non',
        'found' => 'trouvé',
        'not-found' => 'non trouvé',
        'upload' => 'télécharger',
        'upload-file' => 'télécharger un fichier',
        'upload-image' => 'télécharger une image',
        'replace-media' => 'remplacer les médias',
        'store' => 'enregistrer',
        'store-images' => 'enregistrer l\'image|enregistrer les images',
        'details-for' => 'détails pour',
        'view' => 'voir',
        'delete' => 'supprimer',
        'download' => 'télécharger',
        'save' => 'enregistrer',
        'edit' => 'éditer',
        'from' => 'de',
        'to' => 'à',
        'embed' => 'intégrer',
        'loading' => 'chargement',
        'cancel' => 'annuler',
        'update-and-close' => 'mettre à jour et fermer',
        'search' => 'rechercher',
        'confirm' => 'confirmer',
        'create-folder' => 'créer un dossier',
        'create' => 'créer',
        'rename-folder' => 'renommer le dossier',
        'move-folder' => 'déplacer le dossier',
        'move-media' => 'déplacer les médias',
        'delete-folder' => 'supprimer le dossier',
        'sort-by' => 'trier par',
        'regenerate' => 'régénérer',
        'requested' => 'demandé',
        'select-all' => 'tout sélectionner',
        'selected-item-suffix' => 'élément sélectionné',
        'selected-items-suffix-plural' => 'éléments sélectionnés',
    ],

    'warnings' => [
        'delete-media' => 'êtes-vous sûr de vouloir supprimer :filename?',
    ],

    'sentences' => [
        'select-image-to-view-info' => 'sélectionnez un fichier pour voir ses informations.',
        'add-an-alt-text-to-this-image' => 'ajoutez un texte alternatif à cet élément.',
        'add-a-caption-to-this-image' => 'ajoutez une légende / description à cet élément.',
        'enter-search-term' => 'entrez un terme à rechercher',
        'enter-folder-name' => 'entrez un terme pour le dossier',
        'folder-files' => '{0} Le dossier est vide|{1} 1 élément|[2,*] :count éléments',
    ],

    'media' => [
        'choose-image' => 'choisir une image|choisir des images',
        'no-image-selected-yet' => 'aucun élément sélectionné pour le moment.',
        'storing-files' => 'stockage des fichiers...',
        'clear-image' => 'effacer',
        'warning-unstored-uploads' => 'N\'oubliez pas de cliquer sur \'enregistrer\' pour télécharger votre fichier|N\'oubliez pas de cliquer sur \'enregistrer\' pour télécharger vos fichiers',
        'will-be-available-soon' => 'Vos médias seront bientôt disponibles',

        'no-images-found' => [
            'title' => 'aucune image trouvée',
            'description' => 'commencez par télécharger votre premier élément.',
        ],
    ],

    'components' => [
        'browse-library' => [
            'breadcrumbs' => [
                'root' => 'Bibliothèque multimédia',
            ],
            'modals' => [
                'create-media-folder' => [
                    'heading' => 'Créer un dossier',
                    'subheading' => 'Le dossier sera créé dans le dossier actuel.',
                    'form' => [
                        'name' => [
                            'placeholder' => 'Nom du dossier',
                        ],
                    ],
                    'messages' => [
                        'created' => [
                            'body' => 'Dossier multimédia créé',
                        ],
                    ],
                ],
                'rename-media-folder' => [
                    'heading' => 'Entrez un nouveau nom pour ce dossier',
                    'form' => [
                        'name' => [
                            'placeholder' => 'Nom du dossier',
                        ],
                    ],
                    'messages' => [
                        'renamed' => [
                            'body' => 'Dossier multimédia renommé',
                        ],
                    ],
                ],
                'move-media-folder' => [
                    'heading' => 'Choisissez un nouvel emplacement pour ce dossier',
                    'subheading' => 'Tous les éléments à l\'intérieur du dossier seront également déplacés.',
                    'form' => [
                        'media_library_folder_id' => [
                            'placeholder' => 'Sélectionner la destination',
                        ],
                    ],
                    'messages' => [
                        'moved' => [
                            'body' => 'Dossier multimédia déplacé',
                        ],
                    ],
                ],
                'delete-media-folder' => [
                    'heading' => 'Êtes-vous sûr de vouloir supprimer ce dossier?',
                    'subheading' => 'Tous les fichiers dans le dossier ne seront pas supprimés, mais déplacés vers le dossier actuel.',
                    'form' => [
                        'fields' => [
                            'include_children' => [
                                'label' => 'Supprimer tout le contenu dans le dossier',
                                'helper_text' => 'Avertissement : cela supprimera tous les éléments dans le dossier. Cela ne peut pas être annulé.',
                            ],
                        ],
                    ],
                    'messages' => [
                        'deleted' => [
                            'body' => 'Dossier multimédia supprimé',
                        ],
                    ],
                ],
            ],
            'sort_order' => [
                'created_at_ascending' => 'Le plus ancien',
                'created_at_descending' => 'Le plus récent',
                'name_ascending' => 'Nom (A-Z)',
                'name_descending' => 'Nom (Z-A)',
            ],
        ],
        'media-info' => [
            'heading' => 'Voir l\'élément',
            'move-media-item-form' => [
                'fields' => [
                    'media_library_folder_id' => [
                        'placeholder' => 'Sélectionner la destination',
                    ],
                ],
                'messages' => [
                    'moved' => [
                        'body' => 'Élément multimédia déplacé',
                    ],
                ],
            ],
        ],
        'media-picker' => [
            'title' => 'bibliothèque multimédia',
        ],
    ],

    'filament-tip-tap' => [
        'actions' => [
            'media-library-action' => [
                'modal-heading' => 'Choisir des médias',
                'modal-submit-action-label' => 'Sélectionner',
            ],
        ],
    ],
];
