{{-- 
    add 
        data-toggle="modal"
        data-target="#modal-default"
        data-url="{{route('testPopUp')}}"
        class="fire-popup"

--}}
{{-- <button class="fire-popup" data-toggle="modal" data-target="#modal-default" data-url="{{route('testPopUp')}}">
    Launch Default Modal
</button> --}}

<div class="modal fade my-popup" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">T</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>B&hellip;</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade my-popup" id="modal-default-big">
    <div class="modal-dialog" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">T</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>B&hellip;</p>
            </div>
        </div>
    </div>
</div>