@extends('layouts.app')
@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="contact-us container">
      <div class="mw-930">
        <h2 class="page-title">About Us</h2>
      </div>

      <div class="about-us__content pb-5 mb-5">
        <p class="mb-5">
          <img loading="lazy" class="w-100 h-auto d-block" src="{{ asset('assets/images/about/about-1.jpg') }}" width="1410"
            height="550" alt="Fashion boutique interior" />
        </p>
        <div class="mw-930">
          <h3 class="mb-4">OUR STORY</h3>
          <p class="fs-6 fw-medium mb-4">Founded in 2018, our journey began with a simple belief: every woman deserves to feel confident, beautiful, and empowered through fashion. What started as a small boutique has grown into a trusted destination for contemporary women's fashion, offering carefully curated pieces that celebrate individuality and style.</p>
          
          <p class="mb-4">We understand that fashion is more than just clothing—it's a form of self-expression, a confidence booster, and a way to tell your unique story. That's why we're dedicated to bringing you the latest trends, timeless classics, and versatile pieces that seamlessly transition from day to night, work to weekend. Our team travels the world to source premium fabrics and work with skilled artisans who share our commitment to quality and sustainability.</p>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <h5 class="mb-3">Our Mission</h5>
              <p class="mb-3">To empower women through fashion by providing high-quality, stylish, and affordable clothing that makes every woman feel confident and beautiful, regardless of size, age, or personal style preferences.</p>
            </div>
            <div class="col-md-6">
              <h5 class="mb-3">Our Vision</h5>
              <p class="mb-3">To become the leading online destination for women's fashion, known for our exceptional customer service, sustainable practices, and inclusive approach to style that celebrates every woman's unique beauty.</p>
            </div>
          </div>
        </div>
        
        <div class="mw-930 d-lg-flex align-items-lg-center mb-5">
          <div class="image-wrapper col-lg-6">
            <img class="h-auto" loading="lazy" src="{{ asset('assets/images/slideshow-character2.png') }}" width="450" height="500" alt="Our design team at work">
          </div>
          <div class="content-wrapper col-lg-6 px-lg-4">
            <h5 class="mb-3">Our Values</h5>
            <p class="mb-3"><strong>Quality First:</strong> We believe in offering only the finest materials and craftsmanship, ensuring every piece in your wardrobe stands the test of time.</p>
            <p class="mb-3"><strong>Inclusivity:</strong> Fashion is for everyone. We celebrate diversity and offer styles that flatter all body types and personal preferences.</p>
            <p class="mb-3"><strong>Sustainability:</strong> We're committed to ethical fashion practices, working with suppliers who share our values of environmental responsibility.</p>
            <p><strong>Customer Care:</strong> Your satisfaction is our priority. From personalized styling advice to hassle-free returns, we're here to make your shopping experience exceptional.</p>
          </div>
        </div>

        <div class="mw-930">
          <h3 class="mb-4">WHAT SETS US APART</h3>
          <div class="row mb-4">
            <div class="col-md-4 mb-3">
              <h6 class="fw-bold">Curated Collections</h6>
              <p class="small">Hand-picked by our fashion experts, every item in our store is selected for its quality, style, and versatility.</p>
            </div>
            <div class="col-md-4 mb-3">
              <h6 class="fw-bold">Perfect Fit Guarantee</h6>
              <p class="small">With detailed size guides and our easy exchange policy, we ensure you find your perfect fit every time.</p>
            </div>
            <div class="col-md-4 mb-3">
              <h6 class="fw-bold">Personal Styling</h6>
              <p class="small">Our style consultants are available to help you create looks that express your personal aesthetic and lifestyle.</p>
            </div>
          </div>
        </div>

        <div class="mw-930 d-lg-flex align-items-lg-center">
          <div class="content-wrapper col-lg-6 px-lg-4 order-lg-2">
            <h5 class="mb-3">Join Our Community</h5>
            <p class="mb-3">When you shop with us, you're not just buying clothes—you're joining a community of confident, stylish women who support and inspire each other. Follow us on social media for daily style inspiration, behind-the-scenes content, and exclusive previews of new collections.</p>
            <p class="mb-4">We love seeing how you style our pieces! Tag us in your photos for a chance to be featured on our website and social channels.</p>
            <div class="d-flex gap-3">
              <a href="#" class="btn btn-outline-dark btn-sm">Follow on Instagram</a>
              <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm">Start shopping</a>
            </div>
          </div>
          <div class="image-wrapper col-lg-6 order-lg-1">
            <img class="h-auto" loading="lazy" src="{{ asset('assets/images/slideshow-character1.png') }}" width="450" height="500" alt="Happy customers wearing our clothes">
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection