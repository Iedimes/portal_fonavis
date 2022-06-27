<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">{{ trans('brackets/admin-ui::admin.sidebar.content') }}</li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/modalities') }}"><i class="nav-icon icon-plane"></i> {{ trans('admin.modality.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/lands') }}"><i class="nav-icon icon-globe"></i> {{ trans('admin.land.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/documents') }}"><i class="nav-icon icon-umbrella"></i> {{ trans('admin.document.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/categories') }}"><i class="nav-icon icon-umbrella"></i> {{ trans('admin.category.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/project-types') }}"><i class="nav-icon icon-plane"></i> {{ trans('admin.project-type.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/stages') }}"><i class="nav-icon icon-puzzle"></i> {{ trans('admin.stage.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/typologies') }}"><i class="nav-icon icon-compass"></i> {{ trans('admin.typology.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/parentescos') }}"><i class="nav-icon icon-graduation"></i> {{ trans('admin.parentesco.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/discapacidads') }}"><i class="nav-icon icon-ghost"></i> {{ trans('admin.discapacidad.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/modality-has-lands') }}"><i class="nav-icon icon-globe"></i> {{ trans('admin.modality-has-land.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/land-has-project-types') }}"><i class="nav-icon icon-book-open"></i> {{ trans('admin.land-has-project-type.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/assignments') }}"><i class="nav-icon icon-plane"></i> {{ trans('admin.assignment.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/project-type-has-typologies') }}"><i class="nav-icon icon-drop"></i> {{ trans('admin.project-type-has-typology.title') }}</a></li>
           {{-- Do not delete me :) I'm used for auto-generation menu items --}}

            <li class="nav-title">{{ trans('brackets/admin-ui::admin.sidebar.settings') }}</li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/admin-users') }}"><i class="nav-icon icon-user"></i> {{ __('Manage access') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/translations') }}"><i class="nav-icon icon-location-pin"></i> {{ __('Translations') }}</a></li>
            {{-- Do not delete me :) I'm also used for auto-generation menu items --}}
            {{--<li class="nav-item"><a class="nav-link" href="{{ url('admin/configuration') }}"><i class="nav-icon icon-settings"></i> {{ __('Configuration') }}</a></li>--}}
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
