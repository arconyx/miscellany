@inject('formService', 'App\Services\FormService')

{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>{{ trans('crud.panels.general_information') }}</h4>
            </div>
            <div class="panel-body">
                <div class="form-group required">
                    <label>{{ trans('events.fields.name') }}</label>
                    {!! Form::text('name', $formService->prefill('name', $source), ['placeholder' => trans('events.placeholders.name'), 'class' => 'form-control', 'maxlength' => 191]) !!}
                </div>
                @include('cruds.fields.type', ['base' => \App\Models\Event::class, 'trans' => 'events'])
                <div class="form-group">
                    <label>{{ trans('events.fields.date') }}</label>
                    {!! Form::text('date', $formService->prefill('date', $source), ['placeholder' => trans('events.placeholders.date'), 'class' => 'form-control', 'maxlength' => 191]) !!}
                </div>
                @include('cruds.fields.location')
                @include('cruds.fields.tags')
                @include('cruds.fields.attribute_template')

                @if (Auth::user()->isAdmin())
                    <hr>
                    @include('cruds.fields.private')
                @endif
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>{{ trans('crud.panels.appearance') }}</h4>
            </div>
            <div class="panel-body">
                @include('cruds.fields.image')
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @include('cruds.fields.entry')
        @include('cruds.fields.copy')
    </div>
</div>

@include('cruds.fields.save')