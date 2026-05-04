@extends('layouts.app')

@section('title', 'Request a Quote - Forus Freight')

@section('content')
<!-- Hero Section -->
<section style="position: relative; padding: 4rem 0; background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%);">
    <div class="container" style="position: relative; z-index: 10;">
        <div style="max-width: 700px; margin: 0 auto; text-align: center; color: white;">
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Request a Free Quote</h1>
            <p style="font-size: 1.125rem; color: rgba(255,255,255,0.9);">
                Get an instant quote for your logistics needs. We'll respond within 2 hours!
            </p>
        </div>
    </div>
</section>

<!-- Quote Form Section -->
<section style="padding: 5rem 0; background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 4rem; align-items: start;">
            <!-- Left Side - Info Cards -->
            <div>
                <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 2rem;">Why Choose Us?</h2>
                
                <!-- Fast Response Card -->
                <div style="background: #007f7f; color: white; border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,127,127,0.2); border: none;">
                    <div style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <i class="fas fa-bolt" style="color: white; font-size: 1.75rem;"></i>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: white; margin-bottom: 0.75rem;">Fast Response</h3>
                    <p style="color: rgba(255, 255, 255, 0.9); line-height: 1.6;">
                        Get a detailed quote within 2 hours. Our team is ready to assist you with competitive pricing.
                    </p>
                </div>
                
                <!-- Secure & Insured Card -->
                <div style="background: #ff6200; color: white; border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(255,98,0,0.2); border: none;">
                    <div style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <i class="fas fa-shield-alt" style="color: white; font-size: 1.75rem;"></i>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: white; margin-bottom: 0.75rem;">Secure & Insured</h3>
                    <p style="color: rgba(255, 255, 255, 0.9); line-height: 1.6;">
                        All shipments are fully insured. Your cargo's safety is our top priority.
                    </p>
                </div>
                
                <!-- 24/7 Support Card -->
                <div style="background: #cccccc; color: #1e293b; border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: none;">
                    <div style="width: 60px; height: 60px; background: rgba(0, 0, 0, 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <i class="fas fa-headset" style="color: #1e293b; font-size: 1.75rem;"></i>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">24/7 Support</h3>
                    <p style="color: #1e293b; opacity: 0.9; line-height: 1.6;">
                        Our customer service team is available round the clock to assist you.
                    </p>
                </div>
                
                <!-- Contact Info -->
                <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 20px; padding: 2rem; color: white;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">Need Immediate Assistance?</h3>
                    <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
                        <i class="fas fa-phone" style="color: #059669;"></i>
                        <span>+260 96 123 4567</span>
                    </div>
                    <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
                        <i class="fas fa-envelope" style="color: #059669;"></i>
                        <span>quotes@forusfreight.co.zm</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <i class="fab fa-whatsapp" style="color: #059669;"></i>
                        <span>WhatsApp: +260 96 123 4567</span>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Quote Form -->
            <div style="background: white; border-radius: 24px; padding: 3rem; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <form id="quoteForm" action="{{ route('quote.submit') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Service Type *</label>
                        <select name="service_type" required style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;">
                            <option value="">Select a service</option>
                            <option value="same-day">Same-Day Delivery</option>
                            <option value="cross-border">Cross-Border Shipping</option>
                            <option value="warehousing">Warehousing & Storage</option>
                            <option value="bulk-cargo">Bulk Cargo Transport</option>
                            <option value="express">Express Delivery</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Full Name *</label>
                            <input type="text" name="full_name" required style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;" placeholder="John Doe">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Company Name</label>
                            <input type="text" name="company" style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;" placeholder="Your Company">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Email Address *</label>
                            <input type="email" name="email" required style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;" placeholder="john@example.com">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Phone Number *</label>
                            <input type="tel" name="phone" required style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;" placeholder="+260 96 123 4567">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Pickup Location *</label>
                            <input type="text" name="pickup" required style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;" placeholder="Lusaka">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Delivery Location *</label>
                            <input type="text" name="delivery" required style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;" placeholder="Ndola">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Weight (kg)</label>
                            <input type="number" name="weight" style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;" placeholder="50">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Dimensions (LxWxH cm)</label>
                            <input type="text" name="dimensions" style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s;" placeholder="100x50x50">
                        </div>
                    </div>
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Additional Details</label>
                        <textarea name="details" rows="4" style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; resize: vertical; transition: all 0.3s;" placeholder="Please provide any additional information about your shipment..."></textarea>
                    </div>
                    <button type="submit" style="width: 100%; padding: 1.25rem; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none; border-radius: 12px; font-size: 1.125rem; font-weight: 700; cursor: pointer; transition: all 0.3s;">
                        <i class="fas fa-paper-plane" style="margin-right: 0.5rem;"></i> Submit Quote Request
                    </button>
                </form>
                <!-- Success Message -->
                <div id="successMessage" style="display: none; margin-top: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-radius: 12px; text-align: center; border: 2px solid #059669;">
                    <i class="fas fa-check-circle" style="color: #059669; font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Quote Request Submitted!</h3>
                    <p style="color: #64748b;">We'll get back to you within 2 hours with a detailed quote.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #007f7f !important;
        box-shadow: 0 0 0 3px rgba(0, 127, 127, 0.1);
    }
    
    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
    }
    
    @media (max-width: 1024px) {
        section > .container > div[style*="grid-template-columns: 1fr 1.2fr"] {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    document.getElementById('quoteForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        submitBtn.disabled = true;
        
        // Simple form validation
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#ef4444';
            } else {
                field.style.borderColor = '#e2e8f0';
            }
        });
        
        if (!isValid) {
            alert('Please fill in all required fields.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        // Get form data
        const formData = new FormData(this);
        
        // Submit form via AJAX
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                const successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';
                this.reset();
                
                // Scroll to success message
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Hide success message after 5 seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
            } else {
                throw new Error(data.message || 'Form submission failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error submitting your request. Please try again or contact us directly.');
        })
        .finally(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
</script>
@endsection