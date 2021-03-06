<p>{{ trans('quests.hints.quests') }}</p>
<table id="quest-quests" class="table table-hover">
    <tbody><tr>
        <th class="avatar"><br /></th>
        <th>{{ trans('quests.fields.name') }}</th>
        <th>{{ trans('quests.fields.type') }}</th>
        <th>&nbsp;</th>
    </tr>
    @foreach ($r = $model->quests()->acl()->orderBy('name', 'ASC')->paginate() as $model)
        <tr>
            <td>
                <a class="entity-image" style="background-image: url('{{ $model->getImageUrl(true) }}');" title="{{ $model->name }}" href="{{ route('quests.show', $model->id) }}"></a>
            </td>
            <td>
                <a href="{{ route('quests.show', $model->id) }}" data-toggle="tooltip" title="{{ $model->tooltipWithName() }}" data-html="true">{{ $model->name }}</a>
            </td>
            <td>
                {{ $model->type }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $r->fragment('tab_quests')->links() }}