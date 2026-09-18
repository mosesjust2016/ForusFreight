<?php

return [

    /*
    |--------------------------------------------------------------------------
    | China warehouse / forwarding address
    |--------------------------------------------------------------------------
    |
    | Clients send their online orders (Alibaba, etc.) to this warehouse. It is
    | shown in the client "Getting Started" walkthrough, the "Create New
    | Shipment" page and the Help Center.
    |
    | Special announcement: all Forus Freight customers must add our Shipping
    | Mark "ZMFFL" on their consignments before delivering to the China
    | warehouse, and also capture the customer name and contact details.
    | "ZMFFL" routes parcels to ZAMBIA; "GHFFL" would route them to GHANA.
    |
    */

    'forwarding_address' => [
        'name'       => 'Forus Freight Warehouse',
        'line1'      => 'B18, Lijin Logistics Park',
        'line2'      => 'No.3 Yanjiang Road, Dabu, Lishui Town',
        'line3'      => 'Nanhai District, Foshan City',
        'country'    => 'China',
        'address_cn' => '佛山市南海区里水镇大步沿江路3号力进物流园B18仓',
        'phone'      => '+86 18922752986',
        'email'      => 'china@forusfl.co.zm',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping mark / customer code
    |--------------------------------------------------------------------------
    | Auto-generated code of the form "ZMFFL <6 digits>" (e.g. ZMFFL 123456).
    | Customers write this as the Shipping Mark together with their name and
    | contact details, so parcels are routed to Zambia, not Ghana.
    */
    'parcel_code_prefix' => 'ZMFFL',

    /*
    |--------------------------------------------------------------------------
    | Canonical shipment tracking statuses
    |--------------------------------------------------------------------------
    | Single source of truth for shipment status. The key (CODE) is what is
    | stored in shipments.status / tracking_events.status; the value is the
    | customer-facing label. ORDER matters (stage order for the timeline).
    */
    'tracking_statuses' => [
        'CREATED'              => 'Shipment Created',
        'AWAITING_RECEIPT'     => 'Awaiting Arrival at China Warehouse',
        'RECEIVED_CN'          => 'Received at China Warehouse',
        'PROCESSING'           => 'Shipment Being Processed',
        'CONSOLIDATED'         => 'Shipment Consolidated',
        'READY_TO_SHIP'        => 'Ready for Shipment',
        'DEPARTED_CN'          => 'Departed China',
        'IN_TRANSIT'           => 'In Transit to Zambia',
        'ARRIVED_ZM'           => 'Arrived in Zambia',
        'CUSTOMS_CLEARANCE'    => 'Customs Clearance',
        'CLEARED'              => 'Customs Cleared',
        'READY_FOR_COLLECTION' => 'Ready for Collection',
        'OUT_FOR_DELIVERY'     => 'Out for Delivery',
        'DELIVERED'            => 'Delivered',
        'ON_HOLD'              => 'Shipment on Hold',
        'EXCEPTION'            => 'Shipment Exception',
    ],

    /*
    | Progress estimate per canonical status (fallback used when there isn't
    | enough date data to compute a percentage). Must stay in sync with
    | tracking_statuses order.
    */
    'tracking_status_progress' => [
        'CREATED'              => 5,
        'AWAITING_RECEIPT'     => 10,
        'RECEIVED_CN'          => 20,
        'PROCESSING'           => 30,
        'CONSOLIDATED'         => 40,
        'READY_TO_SHIP'        => 50,
        'DEPARTED_CN'          => 55,
        'IN_TRANSIT'           => 60,
        'ARRIVED_ZM'           => 70,
        'CUSTOMS_CLEARANCE'    => 75,
        'CLEARED'              => 80,
        'READY_FOR_COLLECTION' => 85,
        'OUT_FOR_DELIVERY'     => 90,
        'DELIVERED'            => 100,
        'ON_HOLD'              => 50,
        'EXCEPTION'            => 0,
    ],

    /*
    | Map legacy/import status spellings onto canonical codes so old rows
    | keep displaying correctly until they are updated.
    */
    'tracking_status_legacy_map' => [
        'Order Placed'          => 'CREATED',
        'Ordered'               => 'CREATED',
        'ORDERED'               => 'CREATED',
        'ORDER PLACED'          => 'CREATED',
        'Pending'               => 'AWAITING_RECEIPT',
        'Loaded'                => 'READY_TO_SHIP',
        'In Transit'            => 'IN_TRANSIT',
        'IN TRANSIT'            => 'IN_TRANSIT',
        'At Sea'                => 'IN_TRANSIT',
        'At Border'             => 'ARRIVED_ZM',
        'Arrived at Port'       => 'ARRIVED_ZM',
        'AT PORT'               => 'ARRIVED_ZM',
        'Received at China Warehouse' => 'RECEIVED_CN',
        'In Warehouse'          => 'RECEIVED_CN',
        'Processing'            => 'PROCESSING',
        'Consolidated'          => 'CONSOLIDATED',
        'Ready for Shipment'    => 'READY_TO_SHIP',
        'Departed China'        => 'DEPARTED_CN',
        'Customs Clearance'     => 'CUSTOMS_CLEARANCE',
        'CUSTOMS CLEARANCE'     => 'CUSTOMS_CLEARANCE',
        'Cleared'               => 'CLEARED',
        'Customs Cleared'       => 'CLEARED',
        'Ready for Collection'  => 'READY_FOR_COLLECTION',
        'Out for Delivery'      => 'OUT_FOR_DELIVERY',
        'Delivered'             => 'DELIVERED',
        'DELIVERED'             => 'DELIVERED',
        'Cancelled'             => 'EXCEPTION',
        'Shipment on Hold'      => 'ON_HOLD',
        'Shipment Exception'    => 'EXCEPTION',
    ],

];