<div>
    <a href="#" 
    class="btn btn-default add2cart add-to-cart {{ $isOutOfStock ? 'disabled' : '' }}" 
    wire:click.prevent="add"
    {{ $isOutOfStock ? 'disabled' : '' }}>
     <i class="fa fa-shopping-cart"></i>
 </a>
 </div>
