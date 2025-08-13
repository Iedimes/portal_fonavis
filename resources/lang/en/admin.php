<?php

return [
    'admin-user' => [
        'title' => 'Users',

        'actions' => [
            'index' => 'Users',
            'create' => 'New User',
            'edit' => 'Edit :name',
            'edit_profile' => 'Edit Profile',
            'edit_password' => 'Edit Password',
        ],

        'columns' => [
            'id' => 'ID',
            'last_login_at' => 'Last login',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'password' => 'Password',
            'password_repeat' => 'Password Confirmation',
            'activated' => 'Activated',
            'forbidden' => 'Forbidden',
            'language' => 'Language',
                
            //Belongs to many relations
            'roles' => 'Roles',
                
        ],
    ],

    'modality' => [
        'title' => 'Modalities',

        'actions' => [
            'index' => 'Modalities',
            'create' => 'New Modality',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'land' => [
        'title' => 'Lands',

        'actions' => [
            'index' => 'Lands',
            'create' => 'New Land',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'short_name' => 'Short name',
            
        ],
    ],

    'document' => [
        'title' => 'Documents',

        'actions' => [
            'index' => 'Documents',
            'create' => 'New Document',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            
        ],
    ],

    'category' => [
        'title' => 'Categories',

        'actions' => [
            'index' => 'Categories',
            'create' => 'New Category',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'project-type' => [
        'title' => 'Project Type',

        'actions' => [
            'index' => 'Project Type',
            'create' => 'New Project Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'short_name' => 'Short name',
            
        ],
    ],

    'stage' => [
        'title' => 'Stages',

        'actions' => [
            'index' => 'Stages',
            'create' => 'New Stage',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'typology' => [
        'title' => 'Typologies',

        'actions' => [
            'index' => 'Typologies',
            'create' => 'New Typology',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'parentesco' => [
        'title' => 'Parentesco',

        'actions' => [
            'index' => 'Parentesco',
            'create' => 'New Parentesco',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'discapacidad' => [
        'title' => 'Discapacidad',

        'actions' => [
            'index' => 'Discapacidad',
            'create' => 'New Discapacidad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'modality-has-land' => [
        'title' => 'Modality Has Lands',

        'actions' => [
            'index' => 'Modality Has Lands',
            'create' => 'New Modality Has Land',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'modality_id' => 'Modality',
            'land_id' => 'Land',
            
        ],
    ],

    'land-has-project-type' => [
        'title' => 'Land Has Project Type',

        'actions' => [
            'index' => 'Land Has Project Type',
            'create' => 'New Land Has Project Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'land_id' => 'Land',
            'project_type_id' => 'Project type',
            
        ],
    ],

    'assignment' => [
        'title' => 'Assignments',

        'actions' => [
            'index' => 'Assignments',
            'create' => 'New Assignment',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'document_id' => 'Document',
            'category_id' => 'Category',
            'project_type_id' => 'Project type',
            'stage_id' => 'Stage',
            
        ],
    ],

    'project-type-has-typology' => [
        'title' => 'Project Type Has Typologies',

        'actions' => [
            'index' => 'Project Type Has Typologies',
            'create' => 'New Project Type Has Typology',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'project_type_id' => 'Project type',
            'typology_id' => 'Typology',
            
        ],
    ],

    'user' => [
        'title' => 'Users',

        'actions' => [
            'index' => 'Users',
            'create' => 'New User',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
            'sat_ruc' => 'Sat ruc',
            
        ],
    ],

    'project' => [
        'title' => 'Projects',

        'actions' => [
            'index' => 'Projects',
            'create' => 'New Project',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'sat_id' => 'Sat',
            'state_id' => 'State',
            'city_id' => 'City',
            'modalidad_id' => 'Modalidad',
            'leader_name' => 'Leader name',
            'localidad' => 'Localidad',
            'land_id' => 'Land',
            'typology_id' => 'Typology',
            'action' => 'Action',
            'expsocial' => 'Expsocial',
            'exptecnico' => 'Exptecnico',
            
        ],
    ],

    'document-check' => [
        'title' => 'Document Checks',

        'actions' => [
            'index' => 'Document Checks',
            'create' => 'New Document Check',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'project_id' => 'Project',
            'document_id' => 'Document',
            
        ],
    ],

    'project-status' => [
        'title' => 'Project Status',

        'actions' => [
            'index' => 'Project Status',
            'create' => 'New Project Status',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'project_id' => 'Project',
            'stage_id' => 'Stage',
            'user_id' => 'User',
            'record' => 'Record',
            
        ],
    ],

    'postulante' => [
        'title' => 'Postulantes',

        'actions' => [
            'index' => 'Postulantes',
            'create' => 'New Postulante',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'cedula' => 'Cedula',
            'marital_status' => 'Marital status',
            'nacionalidad' => 'Nacionalidad',
            'gender' => 'Gender',
            'birthdate' => 'Birthdate',
            'localidad' => 'Localidad',
            'asentamiento' => 'Asentamiento',
            'ingreso' => 'Ingreso',
            'address' => 'Address',
            'grupo' => 'Grupo',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'nexp' => 'Nexp',
            
        ],
    ],

    'comentario' => [
        'title' => 'Comentarios',

        'actions' => [
            'index' => 'Comentarios',
            'create' => 'New Comentario',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'postulante_id' => 'Postulante',
            'cedula' => 'Cedula',
            'comentario' => 'Comentario',
            
        ],
    ],

    'motivo' => [
        'title' => 'Motivos',

        'actions' => [
            'index' => 'Motivos',
            'create' => 'New Motivo',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'project_id' => 'Project',
            'motivo' => 'Motivo',
            
        ],
    ],

    'admin-users-dependency' => [
        'title' => 'Admin Users Dependencies',

        'actions' => [
            'index' => 'Admin Users Dependencies',
            'create' => 'New Admin Users Dependency',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'admin_user_id' => 'Admin user',
            'dependency_id' => 'Dependency',
            
        ],
    ],

    'dependency' => [
        'title' => 'Dependencies',

        'actions' => [
            'index' => 'Dependencies',
            'create' => 'New Dependency',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'reporte' => [
        'title' => 'Reporte',

        'actions' => [
            'index' => 'Reporte',
            'create' => 'New Reporte',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'inicio' => 'Inicio',
            'fin' => 'Fin',
            'sat_id' => 'Sat',
            'state_id' => 'State',
            'city_id' => 'City',
            'modalidad_id' => 'Modalidad',
            'stage_id' => 'Stage',
            
        ],
    ],

    'project-has-expediente' => [
        'title' => 'Project Has Expedientes',

        'actions' => [
            'index' => 'Project Has Expedientes',
            'create' => 'New Project Has Expediente',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'project_id' => 'Project',
            'exp' => 'Exp',
            
        ],
    ],

    'project-old' => [
        'title' => 'Project Olds',

        'actions' => [
            'index' => 'Project Olds',
            'create' => 'New Project Old',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'project_id' => 'Project',
            'name' => 'Name',
            
        ],
    ],

    // Do not delete me :) I'm used for auto-generation
];