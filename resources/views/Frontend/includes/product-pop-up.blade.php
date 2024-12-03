<!-- Fancybox Modal -->
<div id="product-pop-up" style="display: none; width: 700px;">
  <div class="product-page product-pop-up">
    <div class="row">
      <div class="col-md-6 col-sm-6 col-xs-3">
        <div class="product-main-image">
          <img src="{{ asset('assets') }}/pages/img/products/model2.jpg" alt="" class="img-responsive" id="main-product-image">
        </div>
        <div class="product-other-images">
          <a href="javascript:;" class="active"><img alt="" src="{{ asset('assets') }}/pages/img/products/model2.jpg" id="other-image-1"></a>
          <a href="javascript:;"><img alt="" src="{{ asset('assets') }}/pages/img/products/model2.jpg" id="other-image-2"></a>
          <a href="javascript:;"><img alt="" src="{{ asset('assets') }}/pages/img/products/model2.jpg" id="other-image-3"></a>
        </div>
      </div>
      <div class="col-md-6 col-sm-6 col-xs-9">
        <h2 id="product-name"></h2>
        <div class="price-availability-block clearfix">
          <div class="price">
            <strong><span>{{ trans('frontend.currency') }} </span><span id="product-price"></span></strong>
          </div>
          <div class="availability">
            Availability: <strong id="availability-status"></strong>
          </div>
        </div>
        <div class="description">
          <p id="product-description"></p>
        </div>
        <div class="product-page-cart">
          <div class="product-quantity">
            <input id="product-quantity" type="text" value="1" readonly name="product-quantity" class="form-control input-sm">
          </div>
          <button class="btn btn-primary" id="add-to-cart" data-id="">Add to cart</button>
          <a href="shop-item.html" class="btn btn-default">More details</a>
        </div>
      </div>
    </div>
  </div>
</div>
