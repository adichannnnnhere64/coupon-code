<?php

declare(strict_types=1);

// tests/Feature/CouponListingTest.php
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Operator;
use App\Models\PlanType;
use App\Models\User;

beforeEach(function (): void {
    Country::query()->truncate();
    $this->user = User::factory()->create();
    $this->country = Country::factory()->create(['code' => 'IN']);
    $this->operator = Operator::factory()->forCountry($this->country)->create();
    $this->planType = PlanType::factory()->create();
});

it('can list all available coupons with pagination', function (): void {
    Coupon::factory()->count(25)->forOperator($this->operator)->forPlanType($this->planType)->inStock()->active()->create();

    $response = $this->actingAs($this->user)->getJson('/api/coupons');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'denomination',
                    'selling_price',
                    'coupon_code',
                    'validity_days',
                    'is_available',
                    'is_low_stock',
                    'operator' => ['id', 'name', 'code', 'country'],
                    'plan_type' => ['id', 'name', 'description'],
                ],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(15, 'data'); // Default pagination
});

it('can filter coupons by operator', function (): void {
    $operator2 = Operator::factory()->forCountry($this->country)->create();

    Coupon::factory()->count(5)->forOperator($this->operator)->inStock()->active()->create();
    Coupon::factory()->count(3)->forOperator($operator2)->inStock()->active()->create();

    $response = $this->actingAs($this->user)->getJson("/api/coupons?operator_id={$this->operator->id}");

    $response->assertStatus(200);

    $coupons = $response->json('data');

    foreach ($coupons as $coupon) {
        expect($coupon['operator']['id'])->toBe($this->operator->id);
    }

    foreach ($coupons as $coupon) {
        expect($coupon['operator']['id'])->not->toBe($operator2->id);
    }
});

it('can filter coupons by country', function (): void {
    $usCountry = Country::factory()->create(['code' => 'US']);
    $usOperator = Operator::factory()->forCountry($usCountry)->create();

    Coupon::factory()->count(4)->forOperator($this->operator)->inStock()->active()->create();
    Coupon::factory()->count(2)->forOperator($usOperator)->inStock()->active()->create();

    $response = $this->actingAs($this->user)->getJson("/api/coupons?country_id={$this->country->id}");

    $response->assertStatus(200);

    // All returned coupons should belong to operators in the specified country
    $responseData = $response->json();
    foreach ($responseData['data'] as $coupon) {
        expect($coupon['operator']['country']['id'])->toBe($this->country->id);
    }
});

it('can filter coupons by plan type', function (): void {
    $planType2 = PlanType::factory()->create();

    // Create coupons for both plan types
    Coupon::factory()->count(3)
        ->forPlanType($this->planType)
        ->inStock()
        ->active()
        ->create();

    Coupon::factory()->count(2)
        ->forPlanType($planType2)
        ->inStock()
        ->active()
        ->create();

    // Call the API with filter
    $response = $this->actingAs($this->user)
        ->getJson("/api/coupons?plan_type_id={$this->planType->id}");

    $response->assertStatus(200);

    // Get all returned coupons
    $coupons = $response->json('data');

    // Assert that every returned coupon belongs to the correct plan type
    foreach ($coupons as $coupon) {
        expect($coupon['plan_type']['id'])->toBe($this->planType->id);
    }

    // Assert that none of the returned coupons belong to planType2
    foreach ($coupons as $coupon) {
        expect($coupon['plan_type']['id'])->not->toBe($planType2->id);
    }
});

it('can filter coupons by price range', function (): void {
    Coupon::factory()->create(['selling_price' => 50.00, 'denomination' => 45.00]);
    Coupon::factory()->create(['selling_price' => 150.00, 'denomination' => 140.00]);
    Coupon::factory()->create(['selling_price' => 300.00, 'denomination' => 280.00]);

    $response = $this->actingAs($this->user)->getJson('/api/coupons?min_price=100&max_price=200');

    $response->assertStatus(200);

    $responseData = $response->json();
    foreach ($responseData['data'] as $coupon) {
        expect((float) $coupon['selling_price']['amount'])->toBeGreaterThanOrEqual(100.00);
        expect((float) $coupon['selling_price']['amount'])->toBeLessThanOrEqual(200.00);
    }
});

it('can filter coupons by denomination', function (): void {
    Coupon::factory()->create(['denomination' => 100.00, 'selling_price' => 105.00]);
    Coupon::factory()->create(['denomination' => 200.00, 'selling_price' => 210.00]);
    Coupon::factory()->create(['denomination' => 100.00, 'selling_price' => 108.00]);

    $response = $this->actingAs($this->user)->getJson('/api/coupons?denomination=100');

    $response->assertStatus(200);

    $responseData = $response->json();
    foreach ($responseData['data'] as $coupon) {
        expect((float) $coupon['denomination']['amount'])->toBe(100.00);
    }
});

it('can search coupons by operator name or coupon code', function (): void {
    $specialCoupon = Coupon::factory()->forOperator($this->operator)->create([
        'coupon_code' => 'SPECIAL123',
    ]);

    Coupon::factory()->count(3)->forOperator($this->operator)->create();

    $response = $this->actingAs($this->user)->getJson('/api/coupons?search=SPECIAL');

    $response->assertStatus(200)
        ->assertJsonFragment(['coupon_code' => 'SPECIAL123'])
        ->assertJsonCount(1, 'data');
});

it('can sort coupons by denomination', function (): void {
    Coupon::factory()->create(['denomination' => 500.00]);
    Coupon::factory()->create(['denomination' => 100.00]);
    Coupon::factory()->create(['denomination' => 200.00]);

    $response = $this->actingAs($this->user)->getJson('/api/coupons?sort_by=denomination&sort_order=asc');

    $response->assertStatus(200);

    $responseData = $response->json();
    $denominations = array_column($responseData['data'], 'denomination');

    // Sort ascending and compare
    $sorted = $denominations;
    sort($sorted);

    expect($denominations)->toEqual($sorted);
});

it('can sort coupons by selling price descending', function (): void {
    Coupon::factory()->create(['selling_price' => 50.00]);
    Coupon::factory()->create(['selling_price' => 150.00]);
    Coupon::factory()->create(['selling_price' => 100.00]);

    $response = $this->actingAs($this->user)->getJson('/api/coupons?sort_by=selling_price&sort_order=desc');

    $response->assertStatus(200);

    $responseData = $response->json();
    $prices = array_column($responseData['data'], 'selling_price');

    // Sort descending and compare
    $sorted = $prices;
    rsort($sorted);

    expect($prices)->toEqual($sorted);
});

it('only shows active and in-stock coupons', function (): void {
    Coupon::factory()->inStock()->active()->create(); // Should be shown
    Coupon::factory()->outOfStock()->active()->create(); // Should not be shown
    Coupon::factory()->inStock()->inactive()->create(); // Should not be shown

    $response = $this->actingAs($this->user)->getJson('/api/coupons');

    $response->assertStatus(200);

    $responseData = $response->json();
    foreach ($responseData['data'] as $coupon) {
        expect($coupon['is_available'])->toBeTrue();
    }
});

it('can change pagination per page', function (): void {
    Coupon::factory()->count(25)->forOperator($this->operator)->inStock()->active()->create();

    $response = $this->actingAs($this->user)->getJson('/api/coupons?per_page=5');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.per_page', 5);
});

it('returns empty when no coupons match filters', function (): void {
    $this->withExceptionHandling();
    Coupon::factory()->count(3)->forOperator($this->operator)->create();

    $nonExistentOperatorId = 9999;
    $response = $this->actingAs($this->user)->getJson("/api/coupons?operator_id={$nonExistentOperatorId}");

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

it('requires authentication', function (): void {
    $response = $this->getJson('/api/coupons');

    $response->assertStatus(401);
});
