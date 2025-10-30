<?php

return [
    'file' => 'ملف',
    'meta' => 'الوصف',
    'author' => 'المؤلف',
    'image' => 'صورة',
    'images' => 'صور',
    'information' => 'معلومات',
    'edit-media' => 'تعديل',
    'edit-media-description' => 'احفظ معلومات إضافية لهذا العنصر.',
    'move-media' => 'نقل الوسائط إلى',
    'move-media-description' => 'الموقع الحالي: :name',
    'dimensions' => 'الأبعاد',
    'description' => 'الوصف',
    'type' => 'النوع',
    'caption' => 'التسمية التوضيحية',
    'alt-text' => 'نص بديل',
    'actions' => 'الإجراءات',
    'size' => 'الحجم',
    'page' => 'صفحة|صفحات',
    'duration' => 'المدة',
    'root-folder' => 'المجلد الرئيسي',

    'time' => [
        'created_at' => 'تم الإنشاء في',
        'updated_at' => 'تم التعديل في',
        'published_at' => 'تم النشر في',
        'uploaded_at' => 'تم الرفع في',
        'uploaded_by' => 'تم الرفع بواسطة',
    ],
    'phrases' => [
        'select' => 'اختيار',
        'select-image' => 'اختر صورة',
        'no' => 'لا',
        'found' => 'تم العثور عليه',
        'not-found' => 'غير موجود',
        'upload' => 'رفع',
        'upload-file' => 'رفع ملف',
        'upload-image' => 'رفع صورة',
        'replace-media' => 'استبدال الوسائط',
        'store' => 'حفظ',
        'store-images' => 'حفظ صورة|حفظ صور',
        'details-for' => 'تفاصيل لـ',
        'view' => 'عرض',
        'delete' => 'حذف',
        'download' => 'تنزيل',
        'save' => 'حفظ',
        'edit' => 'تعديل',
        'from' => 'من',
        'to' => 'إلى',
        'embed' => 'تضمين',
        'loading' => 'جاري التحميل',
        'cancel' => 'إلغاء',
        'update-and-close' => 'تحديث وإغلاق',
        'search' => 'بحث',
        'confirm' => 'تأكيد',
        'create-folder' => 'إنشاء مجلد',
        'create' => 'إنشاء',
        'rename-folder' => 'إعادة تسمية المجلد',
        'move-folder' => 'نقل المجلد',
        'move-media' => 'نقل الوسائط',
        'delete-folder' => 'حذف المجلد',
        'sort-by' => 'ترتيب حسب',
        'regenerate' => 'إعادة إنشاء',
        'requested' => 'تم الطلب',
        'select-all' => 'تحديد الكل',
        'selected-item-suffix' => 'عنصر محدد',
        'selected-items-suffix-plural' => 'عناصر محددة',
    ],
    'warnings' => [
        'delete-media' => 'هل أنت متأكد من حذف :filename؟',
    ],
    'sentences' => [
        'select-image-to-view-info' => 'اختر ملفاً لعرض معلوماته.',
        'add-an-alt-text-to-this-image' => 'أضف نصاً بديلاً لهذا العنصر.',
        'add-a-caption-to-this-image' => 'أضف تسمية توضيحية/وصف لهذا العنصر.',
        'enter-search-term' => 'أدخل مصطلح البحث',
        'enter-folder-name' => 'أدخل اسم المجلد',
        'folder-files' => '{0} المجلد فارغ|{1} عنصر واحد|[2,*] :count عناصر',
    ],
    'media' => [
        'choose-image' => 'اختر صورة|اختر صور',
        'no-image-selected-yet' => 'لم يتم اختيار أي عنصر بعد.',
        'storing-files' => 'جاري حفظ الملفات...',
        'clear-image' => 'مسح',
        'warning-unstored-uploads' => 'لا تنس النقر على "حفظ" لرفع الملف|لا تنس النقر على "حفظ" لرفع الملفات',
        'will-be-available-soon' => 'سيكون الوسيط متاحاً قريباً',
        'no-images-found' => [
            'title' => 'لم يتم العثور على صور',
            'description' => 'ابدأ برفع أول عنصر.',
        ],
    ],
    'components' => [
        'browse-library' => [
            'breadcrumbs' => [
                'root' => 'مكتبة الوسائط',
            ],
            'modals' => [
                'create-media-folder' => [
                    'heading' => 'إنشاء مجلد',
                    'subheading' => 'سيتم إنشاء المجلد داخل المجلد الحالي.',
                    'form' => [
                        'name' => [
                            'placeholder' => 'اسم المجلد',
                        ],
                    ],
                    'messages' => [
                        'created' => [
                            'body' => 'تم إنشاء مجلد الوسائط',
                        ],
                    ],
                ],
                'rename-media-folder' => [
                    'heading' => 'أدخل اسماً جديداً لهذا المجلد',
                    'form' => [
                        'name' => [
                            'placeholder' => 'اسم المجلد',
                        ],
                    ],
                    'messages' => [
                        'renamed' => [
                            'body' => 'تمت إعادة تسمية مجلد الوسائط',
                        ],
                    ],
                ],
                'move-media-folder' => [
                    'heading' => 'اختر موقعاً جديداً لهذا المجلد',
                    'subheading' => 'سيتم نقل جميع العناصر داخل المجلد أيضاً.',
                    'form' => [
                        'media_library_folder_id' => [
                            'placeholder' => 'حدد الوجهة',
                        ],
                    ],
                    'messages' => [
                        'moved' => [
                            'body' => 'تم نقل مجلد الوسائط',
                        ],
                    ],
                ],
                'delete-media-folder' => [
                    'heading' => 'هل أنت متأكد أنك تريد حذف هذا المجلد؟',
                    'subheading' => 'لن يتم حذف الملفات داخل المجلد، بل سيتم نقلها إلى المجلد الحالي.',
                    'form' => [
                        'fields' => [
                            'include_children' => [
                                'label' => 'حذف جميع المحتويات في المجلد',
                                'helper_text' => 'تحذير: سيؤدي هذا إلى حذف جميع العناصر في المجلد. لا يمكن التراجع عن هذا الإجراء.',
                            ],
                        ],
                    ],
                    'messages' => [
                        'deleted' => [
                            'body' => 'تم حذف مجلد الوسائط',
                        ],
                    ],
                ],
            ],
            'sort_order' => [
                'created_at_ascending' => 'الأقدم',
                'created_at_descending' => 'الأحدث',
                'name_ascending' => 'الاسم (أ-ي)',
                'name_descending' => 'الاسم (ي-أ)',
            ],
        ],
        'media-info' => [
            'heading' => 'عرض العنصر',
            'move-media-item-form' => [
                'fields' => [
                    'media_library_folder_id' => [
                        'placeholder' => 'حدد الوجهة',
                    ],
                ],
                'messages' => [
                    'moved' => [
                        'body' => 'تم نقل عنصر الوسائط',
                    ],
                ],
            ],
        ],
        'media-picker' => [
            'title' => 'مكتبة الوسائط',
        ],
    ],
    'filament-tip-tap' => [
        'actions' => [
            'media-library-action' => [
                'modal-heading' => 'اختر وسائط',
                'modal-submit-action-label' => 'اختيار',
            ],
        ],
    ],
];
