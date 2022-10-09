<div class="p-4 col-6 col-sm-4 col-md-3 ">
    <div class="product-holder">
        <a href="/product/{{ $slug }}"> <!-- ID will go here -->
            <div class="image-holder">
                <div class="image" style="background-image: url({{'/uploads/'.$image}});">
                </div>
                @if ($quantity <= 0)
                    <div class="out-of-stock">
                        <p>Out of stock</p>
                    </div>
                @endif
                <?php 
                    $origin     = new DateTime("now");
                    $created_at = new DateTime($created);
                    $interval = $origin->diff($created_at)->days; 
                ?>
                @if ($interval <= 7)
                    <div class="new">
                        <p>New</p>
                    </div>
                @endif
            </div>
            <div class="details">
                <div class="name-with-line">
                    <div class="name"> {{ $name }} </div>
                    <div class="line"></div>
                </div>
                <div class="price"> {{ number_format($price, 0, ' ', ' ') }} Ft </div>
            </div>
        </a>
    </div>
</div>