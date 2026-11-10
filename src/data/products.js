export const products = [
  {
    id: 1,
    slug: 'in-danh-thiep-namecard',
    name: 'In Danh Thiếp - Namecard',
    categoryId: 'an-pham-van-phong',
    categoryName: 'Ấn Phẩm Văn Phòng',
    tag: 'Bán chạy nhất',
    basePrice: 50000,
    unit: 'hộp',
    description: 'Danh thiếp offset sắc nét, chuẩn màu chuẩn kích thước, cán màng mờ 2 mặt bảo vệ tối ưu.',
    image: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=800&q=80',
    turnaround: '24h - 48h',
    minQuantity: 2,
    specs: {
      sizes: [
        { label: 'Tiêu chuẩn (9.0 x 5.4 cm)', value: 'standard', extra: 0 },
        { label: 'Kích thước nhỏ (9.0 x 5.0 cm)', value: 'small', extra: 0 },
        { label: 'Bo tròn 4 góc (+30k/hộp)', value: 'rounded', extra: 30000 }
      ],
      materials: [
        { label: 'Giấy Couche 300gsm (Dày dặn, phổ biến)', value: 'c300', extra: 0 },
        { label: 'Giấy Mỹ Thuật vân sần cao cấp', value: 'art_paper', extra: 60000 },
        { label: 'Nhựa Plastic trong suốt / Đục xé không rách', value: 'plastic', extra: 120000 },
        { label: 'Giấy Kraft tái chế vintage', value: 'kraft', extra: 25000 }
      ],
      finishings: [
        { label: 'Cán màng mờ 2 mặt (Khuyên dùng)', value: 'matte', extra: 0 },
        { label: 'Cán màng bóng 2 mặt', value: 'glossy', extra: 0 },
        { label: 'Ép kim vàng / bạc Logo (+120k)', value: 'foil', extra: 120000 },
        { label: 'Dập nổi / chìm định hình Logo (+100k)', value: 'emboss', extra: 100000 }
      ],
      quantities: [
        { label: '2 hộp (200 cái)', count: 2, pricePerUnit: 50000 },
        { label: '5 hộp (500 cái) - Khuyên dùng', count: 5, pricePerUnit: 35000 },
        { label: '10 hộp (1.000 cái) - Giá xưởng', count: 10, pricePerUnit: 25000 },
        { label: '20 hộp (2.000 cái) - Tiết kiệm 60%', count: 20, pricePerUnit: 20000 }
      ]
    },
    features: [
      'Công nghệ in ghép bài Offset công nghiệp chuẩn màu',
      'Đóng hộp mica trong suốt 100 cái/hộp tiện lợi',
      'Miễn phí chỉnh sửa file in cơ bản',
      'Giao hàng tận nơi toàn quốc'
    ]
  },
  {
    id: 2,
    slug: 'in-to-roi-brochure',
    name: 'In Tờ Rơi - Flyer - Brochure',
    categoryId: 'tiep-thi-quang-cao',
    categoryName: 'Tiếp Thị & Quảng Cáo',
    tag: 'In nhanh',
    basePrice: 450,
    unit: 'tờ',
    description: 'Tờ rơi quảng cáo A4, A5 in offset màu sắc tươi sáng, định lượng giấy đa dạng từ C100 đến C250.',
    image: 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
    turnaround: '1 - 2 ngày',
    minQuantity: 500,
    specs: {
      sizes: [
        { label: 'Khổ A5 (14.8 x 21.0 cm)', value: 'a5', extra: 0 },
        { label: 'Khổ A4 (21.0 x 29.7 cm)', value: 'a4', extra: 450 },
        { label: 'Khổ A3 gấp đôi (42.0 x 29.7 cm)', value: 'a3', extra: 1100 }
      ],
      materials: [
        { label: 'Couche 150gsm (Mỏng nhẹ phát tờ rơi)', value: 'c150', extra: 0 },
        { label: 'Couche 200gsm (Dày vừa, sang trọng)', value: 'c200', extra: 200 },
        { label: 'Couche 250gsm (Dày cứng cao cấp)', value: 'c250', extra: 350 }
      ],
      finishings: [
        { label: 'Không cán màng (Tiết kiệm)', value: 'none', extra: 0 },
        { label: 'Cán màng mờ 2 mặt', value: 'matte', extra: 250 },
        { label: 'Cấn 1 đường gấp đôi', value: 'fold_1', extra: 50 },
        { label: 'Cấn 2 đường gấp 3 (Z-fold / C-fold)', value: 'fold_2', extra: 100 }
      ],
      quantities: [
        { label: '500 tờ (In nhanh kỹ thuật số)', count: 500, pricePerUnit: 850 },
        { label: '1.000 tờ (In Offset)', count: 1000, pricePerUnit: 550 },
        { label: '2.000 tờ (Tiết kiệm)', count: 2000, pricePerUnit: 420 },
        { label: '5.000 tờ (Giá sỉ xưởng)', count: 5000, pricePerUnit: 320 }
      ]
    },
    features: [
      'In 2 mặt đầy đủ màu sắc không giới hạn chi tiết',
      'Mực in chống bay màu, bóng đẹp',
      'Nhận in gấp trong ngày với số lượng ít',
      'Hỗ trợ xuất hóa đơn VAT'
    ]
  },
  {
    id: 3,
    slug: 'in-tem-nhan-decal',
    name: 'In Tem Nhãn Decal - Sticker Bế Sẵn',
    categoryId: 'tem-nhan-decal',
    categoryName: 'Tem Nhãn & Decal',
    tag: 'Bế khuôn tự do',
    basePrice: 150,
    unit: 'con',
    description: 'Tem nhãn dán ly trà sữa, chai lọ mỹ phẩm, hộp thực phẩm. Cắt bế khuôn mọi hình dạng tròn, oval, chữ nhật.',
    image: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=800&q=80',
    turnaround: '24h',
    minQuantity: 500,
    specs: {
      sizes: [
        { label: 'Tròn 3cm / Vuông 3x3cm', value: '3cm', extra: 0 },
        { label: 'Tròn 4cm / Vuông 4x4cm', value: '4cm', extra: 80 },
        { label: 'Tròn 5cm / Chữ nhật 4x6cm', value: '5cm', extra: 150 },
        { label: 'Kích thước lớn 6x8cm', value: '6x8cm', extra: 300 }
      ],
      materials: [
        { label: 'Decal Giấy (Bám dính tốt, tiết kiệm)', value: 'paper', extra: 0 },
        { label: 'Decal Nhựa Sữa (Chống thấm nước tuyệt đối)', value: 'pvc_white', extra: 100 },
        { label: 'Decal Trong Suốt (Nhìn xuyên nền chai)', value: 'clear', extra: 120 },
        { label: 'Decal Xi Bạc / Phản quang chống giả', value: 'silver', extra: 250 }
      ],
      finishings: [
        { label: 'Cán màng bóng chống trầy', value: 'glossy', extra: 0 },
        { label: 'Cán màng mờ sang trọng', value: 'matte', extra: 0 },
        { label: 'Ép kim ánh vàng Logo', value: 'foil', extra: 80 }
      ],
      quantities: [
        { label: '500 con tem', count: 500, pricePerUnit: 280 },
        { label: '1.000 con tem', count: 1000, pricePerUnit: 180 },
        { label: '2.000 con tem', count: 2000, pricePerUnit: 140 },
        { label: '5.000 con tem (Giá xưởng)', count: 5000, pricePerUnit: 100 }
      ]
    },
    features: [
      'Đã bế demi sẵn, chỉ việc lột dán dễ dàng',
      'Chống bong tróc khi để ngăn mát, tủ đông',
      'Mực in Nhật Bản an toàn vệ sinh thực phẩm',
      'Bế mọi biên dạng uốn lượn theo logo'
    ]
  },
  {
    id: 4,
    slug: 'in-tui-giay-hop-giay',
    name: 'In Túi Giấy & Hộp Giấy Thương Hiệu',
    categoryId: 'bao-bi-tui-giay',
    categoryName: 'Bao Bì & Hộp Giấy',
    tag: 'Nâng tầm thương hiệu',
    basePrice: 3500,
    unit: 'cái',
    description: 'Túi giấy shop thời trang, mỹ phẩm, túi quà tặng doanh nghiệp có xỏ quai dây dù / dây ruy băng cao cấp.',
    image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
    turnaround: '3 - 5 ngày',
    minQuantity: 200,
    specs: {
      sizes: [
        { label: 'Túi Nhỏ (15 x 20 x 8 cm)', value: 'small', extra: 0 },
        { label: 'Túi Vừa Đứng (25 x 30 x 10 cm)', value: 'medium', extra: 1500 },
        { label: 'Túi Lớn Ngang (35 x 25 x 12 cm)', value: 'large', extra: 3200 }
      ],
      materials: [
        { label: 'Giấy Kraft Nâu thân thiện môi trường (170gsm)', value: 'kraft_brown', extra: 0 },
        { label: 'Giấy Kraft Trắng Nhật Bản (160gsm)', value: 'kraft_white', extra: 800 },
        { label: 'Giấy Couche 250gsm cán màng mờ', value: 'c250', extra: 1800 },
        { label: 'Giấy Ivory 300gsm dày dặn cao cấp', value: 'ivory', extra: 2800 }
      ],
      finishings: [
        { label: 'Quai dây dù tròn đồng màu', value: 'rope', extra: 0 },
        { label: 'Quai dây ruy băng lụa (+1.5k/cái)', value: 'ribbon', extra: 1500 },
        { label: 'Ép kim Logo nổi bật (+2k/cái)', value: 'foil', extra: 2000 }
      ],
      quantities: [
        { label: '200 cái', count: 200, pricePerUnit: 8500 },
        { label: '500 cái (Phổ biến)', count: 500, pricePerUnit: 5800 },
        { label: '1.000 cái (Tối ưu chi phí)', count: 1000, pricePerUnit: 4200 },
        { label: '2.000 cái (Giá gốc xưởng)', count: 2000, pricePerUnit: 3500 }
      ]
    },
    features: [
      'Đáy túi bồi giấy cứng chịu lực từ 3kg - 7kg',
      'Đóng mắt cáo kim loại xỏ dây bền chắc',
      'In offset màu chuẩn nhận diện',
      'Miễn phí thiết kế layout mẫu'
    ]
  },
  {
    id: 5,
    slug: 'in-catalogue-tap-san',
    name: 'In Catalogue - Kỷ Yếu - Sách Ảnh',
    categoryId: 'tiep-thi-quang-cao',
    categoryName: 'Tiếp Thị & Quảng Cáo',
    tag: 'Chuyên nghiệp B2B',
    basePrice: 12000,
    unit: 'cuốn',
    description: 'Catalogue giới thiệu năng lực công ty, profile doanh nghiệp, đóng ghim lồng hoặc may chỉ dán gáy.',
    image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
    turnaround: '2 - 3 ngày',
    minQuantity: 50,
    specs: {
      sizes: [
        { label: 'Khổ A4 Đứng (20.5 x 29.5 cm)', value: 'a4_portrait', extra: 0 },
        { label: 'Khổ A4 Ngang (29.5 x 20.5 cm)', value: 'a4_landscape', extra: 2000 },
        { label: 'Khổ A5 (14.5 x 20.5 cm)', value: 'a5', extra: -3000 }
      ],
      materials: [
        { label: 'Bìa C300 cán màng + Ruột C150 (Chuẩn)', value: 'std_c150', extra: 0 },
        { label: 'Bìa C300 cán màng + Ruột C200 dày dặn', value: 'thick_c200', extra: 3500 },
        { label: 'Bìa Cứng Carton lạnh bồi bọc da/vải', value: 'hardcover', extra: 25000 }
      ],
      finishings: [
        { label: 'Đóng 2 kim giữa (Dưới 32 trang)', value: 'staple', extra: 0 },
        { label: 'Dán keo gáy nhiệt (Trên 36 trang)', value: 'glue', extra: 2500 },
        { label: 'Đóng lò xo kẽm trắng/đen', value: 'spiral', extra: 4000 }
      ],
      quantities: [
        { label: '50 cuốn (In nhanh test mẫu)', count: 50, pricePerUnit: 25000 },
        { label: '100 cuốn', count: 100, pricePerUnit: 18000 },
        { label: '300 cuốn', count: 300, pricePerUnit: 14000 },
        { label: '500 cuốn (Offset giá rẻ)', count: 500, pricePerUnit: 11000 }
      ]
    },
    features: [
      'Gia công chuẩn gáy thẳng hàng, không bung tróc',
      'Cán màng mờ hoặc bóng bảo vệ bìa',
      'In test màu trước khi chạy hàng loạt'
    ]
  },
  {
    id: 6,
    slug: 'in-standee-bang-ron',
    name: 'In Standee - Backdrop - Băng Rôn',
    categoryId: 'su-kien-trien-lam',
    categoryName: 'Sự Kiện & Triển Lãm',
    tag: 'Lấy liền 2h',
    basePrice: 75000,
    unit: 'm²',
    description: 'In PP trong nhà/ngoài trời, hiflex dày dặn, bán kèm chân standee chữ X hoặc chân cuốn nhôm tốt.',
    image: 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=800&q=80',
    turnaround: '2h - 4h',
    minQuantity: 1,
    specs: {
      sizes: [
        { label: 'Standee 60 x 160 cm', value: '60x160', extra: 0 },
        { label: 'Standee 80 x 180 cm', value: '80x180', extra: 45000 },
        { label: 'Standee 80 x 200 cm', value: '80x200', extra: 60000 },
        { label: 'In Hiflex theo m² (Tự nhập kích thước)', value: 'hiflex_sqm', extra: 0 }
      ],
      materials: [
        { label: 'Bạt Hiflex 3.2zem (Băng rôn ngoài trời)', value: 'hiflex', extra: 0 },
        { label: 'Decal PP trong nhà cán màng mờ', value: 'pp_indoor', extra: 30000 },
        { label: 'Decal PP ngoài trời mực dầu bền màu', value: 'pp_outdoor', extra: 50000 },
        { label: 'Bạt 2 da xám chống xuyên sáng', value: 'blockout', extra: 40000 }
      ],
      finishings: [
        { label: 'Chỉ in bạt (Đã đóng khoen 4 góc)', value: 'banner_only', extra: 0 },
        { label: 'Kèm Chân Standee chữ X cường lực', value: 'x_stand', extra: 85000 },
        { label: 'Kèm Chân Standee cuốn nhôm cao cấp', value: 'roll_stand', extra: 160000 }
      ],
      quantities: [
        { label: '1 bộ (In lấy liền)', count: 1, pricePerUnit: 145000 },
        { label: '3 bộ', count: 3, pricePerUnit: 130000 },
        { label: '5 bộ', count: 5, pricePerUnit: 115000 },
        { label: '10 bộ (Giá sỉ sự kiện)', count: 10, pricePerUnit: 95000 }
      ]
    },
    features: [
      'Giao hàng hỏa tốc trong 2 giờ tại TPHCM',
      'Chân cuốn nhôm dày chắc chắn, tái sử dụng nhiều lần',
      'Màu in sắc nét, không gãy gấp'
    ]
  },
  {
    id: 7,
    slug: 'in-bao-thu-phong-bi-thuong-hieu',
    name: 'In Bao Thư - Phong Bì Thương Hiệu',
    categoryId: 'an-pham-van-phong',
    categoryName: 'Ấn Phẩm Văn Phòng',
    tag: 'Chuẩn màu nhận diện',
    basePrice: 1800,
    unit: 'cái',
    description: 'Bao thư nhỏ 12x22cm, trung 17x23cm, lớn A4 25x35cm in offset sắc nét, có dán keo nắp sẵn tiện lợi.',
    image: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=800&q=80',
    turnaround: '2 - 3 ngày',
    minQuantity: 500,
    specs: {
      sizes: [
        { label: 'Bao Thư Nhỏ (12 x 22 cm - Đựng vừa A4 gấp 3)', value: 'small_12x22', extra: 0 },
        { label: 'Bao Thư Trung (16 x 23 cm - Đựng vừa A5)', value: 'medium_16x23', extra: 800 },
        { label: 'Bao Thư Lớn (25 x 35 cm - Đựng hồ sơ A4)', value: 'large_25x35', extra: 1600 }
      ],
      materials: [
        { label: 'Giấy Fort 100gsm (Tiết kiệm, dễ viết bút bi)', value: 'f100', extra: 0 },
        { label: 'Giấy Fort 120gsm (Dày dặn, không lộ nội dung)', value: 'f120', extra: 300 },
        { label: 'Giấy Couche 150gsm (Bóng mịn, sang trọng)', value: 'c150', extra: 700 }
      ],
      finishings: [
        { label: 'Dán băng keo nắp 2 mặt (Tiện lợi)', value: 'tape', extra: 0 },
        { label: 'Cửa sổ kiếng bóng kính (Nhìn thấy địa chỉ)', value: 'window', extra: 450 },
        { label: 'Ép kim ánh vàng Logo (+60k/đơn)', value: 'foil', extra: 600 }
      ],
      quantities: [
        { label: '500 cái (In nhanh)', count: 500, pricePerUnit: 2600 },
        { label: '1.000 cái (Giá xưởng)', count: 1000, pricePerUnit: 2100 },
        { label: '2.000 cái (Tiết kiệm)', count: 2000, pricePerUnit: 1800 },
        { label: '5.000 cái (Giá đại lý)', count: 5000, pricePerUnit: 1450 }
      ]
    },
    features: [
      'Gia công bế dán tự động viền mép chuẩn xác',
      'Băng keo hai mặt sẵn ở nắp chỉ cần lột dán',
      'Chuẩn hệ màu CMYK không lệch logo công ty'
    ]
  },
  {
    id: 8,
    slug: 'in-kep-file-bia-ho-so-folder',
    name: 'In Kẹp File - Bìa Hồ Sơ Folder A4',
    categoryId: 'an-pham-van-phong',
    categoryName: 'Ấn Phẩm Văn Phòng',
    tag: 'B2B Cao Cấp',
    basePrice: 4500,
    unit: 'cái',
    description: 'Folder bìa đựng tài liệu khổ A4 có tai cài danh thiếp, cán màng mờ bảo vệ chống trầy xước.',
    image: 'https://images.unsplash.com/photo-1586075010923-2dd4570fb338?auto=format&fit=crop&w=800&q=80',
    turnaround: '3 - 4 ngày',
    minQuantity: 200,
    specs: {
      sizes: [
        { label: 'Khổ gập 22 x 31 cm (Đựng vừa tài liệu A4)', value: 'folder_std', extra: 0 }
      ],
      materials: [
        { label: 'Bristol 300gsm (Dày dặn, đanh cứng)', value: 'b300', extra: 0 },
        { label: 'Couche 300gsm (Bóng sáng hai mặt)', value: 'c300', extra: 200 },
        { label: 'Ivory 350gsm (Rất cứng cáp, chịu lực cao)', value: 'ivory350', extra: 1200 }
      ],
      finishings: [
        { label: '1 Tai gấp đáy 7cm có khe gài namecard', value: '1_pocket', extra: 0 },
        { label: '2 Tai gấp 2 bên (Đựng nhiều hồ sơ hơn)', value: '2_pockets', extra: 1500 },
        { label: 'Cán màng mờ 1 mặt ngoài', value: 'matte_1side', extra: 0 },
        { label: 'Ép kim ánh vàng Logo công ty', value: 'foil', extra: 1800 }
      ],
      quantities: [
        { label: '200 cái (Số lượng ít)', count: 200, pricePerUnit: 7800 },
        { label: '500 cái (Giá xưởng)', count: 500, pricePerUnit: 5600 },
        { label: '1.000 cái (Tối ưu)', count: 1000, pricePerUnit: 4500 },
        { label: '2.000 cái (Chiết khấu cao)', count: 2000, pricePerUnit: 3900 }
      ]
    },
    features: [
      'Bế khuôn sắc cạnh, gấp nếp thẳng đều thẩm mỹ',
      'Khe cắm card visit tiện lợi khi đi gặp đối tác',
      'Định lượng giấy dày đảm bảo tài liệu không bị quăn góc'
    ]
  },
  {
    id: 9,
    slug: 'in-voucher-phieu-giam-gia-ve',
    name: 'In Voucher - Phiếu Quà Tặng - Coupon',
    categoryId: 'tiep-thi-quang-cao',
    categoryName: 'Tiếp Thị & Quảng Cáo',
    tag: 'Kích cầu doanh số',
    basePrice: 350,
    unit: 'tờ',
    description: 'Voucher giảm giá, thẻ liệu trình spa, vé ca nhạc có đóng số nhảy kiểm soát và răng cưa xé gốc.',
    image: 'https://images.unsplash.com/photo-1556742049-0a67e557224e?auto=format&fit=crop&w=800&q=80',
    turnaround: '24h - 48h',
    minQuantity: 500,
    specs: {
      sizes: [
        { label: 'Khổ 7 x 15 cm (Chuẩn voucher ví tiền)', value: '7x15', extra: 0 },
        { label: 'Khổ 10 x 20 cm (Phiếu quà tặng sang trọng)', value: '10x20', extra: 250 },
        { label: 'Khổ Namecard 9 x 5.4 cm (Thẻ tích điểm)', value: '9x5.4', extra: -100 }
      ],
      materials: [
        { label: 'Couche 250gsm (Cứng cáp, bắt mắt)', value: 'c250', extra: 0 },
        { label: 'Couche 300gsm cán màng mờ', value: 'c300', extra: 150 },
        { label: 'Giấy Mỹ Thuật vân sần cao cấp', value: 'art_paper', extra: 600 }
      ],
      finishings: [
        { label: 'Cắt thẳng thành phẩm', value: 'cut', extra: 0 },
        { label: 'Đóng số nhảy thứ tự + Cấn răng cưa xé', value: 'perforate_number', extra: 250 },
        { label: 'Phủ cào bảo mật trúng thưởng (+200đ)', value: 'scratch', extra: 200 }
      ],
      quantities: [
        { label: '500 cái', count: 500, pricePerUnit: 650 },
        { label: '1.000 cái (Phổ biến)', count: 1000, pricePerUnit: 480 },
        { label: '2.000 cái', count: 2000, pricePerUnit: 390 },
        { label: '5.000 cái (Giá xưởng)', count: 5000, pricePerUnit: 350 }
      ]
    },
    features: [
      'In số nhảy liên tục không trùng lặp chống gian lận',
      'Đường cấn răng cưa sắc nét xé mượt mà',
      'Hỗ trợ thiết kế mã QR Code quét nhanh'
    ]
  },
  {
    id: 10,
    slug: 'in-tem-bao-hanh-tem-be-niem-phong',
    name: 'In Tem Bảo Hành - Tem Bể Niêm Phong',
    categoryId: 'tem-nhan-decal',
    categoryName: 'Tem Nhãn & Decal',
    tag: 'Chống bóc giả mạo',
    basePrice: 120,
    unit: 'con',
    description: 'Decal vỡ dán ốc linh kiện điện tử, mỹ phẩm, dược phẩm. Bóc ra là tự vỡ vụn thành từng mảnh nhỏ.',
    image: 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=800&q=80',
    turnaround: '24h',
    minQuantity: 1000,
    specs: {
      sizes: [
        { label: 'Tròn 1cm / Oval 1x1.5cm', value: '1cm', extra: 0 },
        { label: 'Chữ nhật 1x2cm (Chuẩn bảo hành điện thoại)', value: '1x2cm', extra: 20 },
        { label: 'Chữ nhật 1x2.5cm', value: '1x2.5cm', extra: 40 }
      ],
      materials: [
        { label: 'Decal Vỡ Giòn (Càng để lâu càng giòn tan)', value: 'brittle', extra: 0 },
        { label: 'Decal Vỡ Dẻo (Dán các bề mặt cong/gập)', value: 'flexible', extra: 30 },
        { label: 'Decal 7 Màu Hologram chống giả', value: 'hologram', extra: 90 }
      ],
      finishings: [
        { label: 'Bế demi thành phẩm từng con (Dễ lột)', value: 'kiss_cut', extra: 0 }
      ],
      quantities: [
        { label: '1.000 con', count: 1000, pricePerUnit: 220 },
        { label: '2.000 con', count: 2000, pricePerUnit: 170 },
        { label: '5.000 con', count: 5000, pricePerUnit: 130 },
        { label: '10.000 con (Giá đại lý)', count: 10000, pricePerUnit: 100 }
      ]
    },
    features: [
      'Keo dính cực chắc, niêm phong tuyệt đối',
      'Đã bế sẵn từng con tem chỉ việc bóc dán',
      'Có thể in ngày tháng bảo hành hoặc logo siêu nhỏ cực nét'
    ]
  },
  {
    id: 11,
    slug: 'in-hop-giay-hop-cung-cao-cap',
    name: 'In Hộp Giấy - Hộp Carton - Hộp Cứng',
    categoryId: 'bao-bi-tui-giay',
    categoryName: 'Bao Bì & Hộp Giấy',
    tag: 'Nâng tầm giá trị',
    basePrice: 6500,
    unit: 'hộp',
    description: 'Hộp mỹ phẩm, hộp yến sào, hộp bánh trung thu, hộp quà tặng nắp nam châm âm dương sang trọng.',
    image: 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=800&q=80',
    turnaround: '4 - 7 ngày',
    minQuantity: 100,
    specs: {
      sizes: [
        { label: 'Hộp Nhỏ Mỹ Phẩm (8 x 8 x 4 cm)', value: 'box_small', extra: 0 },
        { label: 'Hộp Vừa Đựng Quà (15 x 15 x 6 cm)', value: 'box_medium', extra: 3500 },
        { label: 'Hộp Lớn Cao Cấp (22 x 30 x 8 cm)', value: 'box_large', extra: 7500 }
      ],
      materials: [
        { label: 'Giấy Ivory 350gsm (Hộp mềm gập mở)', value: 'ivory350', extra: 0 },
        { label: 'Carton sóng E bồi Duplex (Cứng cáp, ship cod)', value: 'corrugated_e', extra: 2000 },
        { label: 'Carton Lạnh cao cấp bồi giấy mỹ thuật', value: 'rigid_board', extra: 8500 }
      ],
      finishings: [
        { label: 'Cán màng mờ chống thấm', value: 'matte', extra: 0 },
        { label: 'Ép kim vàng kim cương / Bạc Logo', value: 'foil', extra: 1500 },
        { label: 'Phủ UV định hình cục bộ hoa văn', value: 'spot_uv', extra: 2000 }
      ],
      quantities: [
        { label: '100 hộp (Lấy mẫu)', count: 100, pricePerUnit: 14000 },
        { label: '300 hộp', count: 300, pricePerUnit: 9500 },
        { label: '500 hộp (Phổ biến)', count: 500, pricePerUnit: 7800 },
        { label: '1.000 hộp (Giá gốc xưởng)', count: 1000, pricePerUnit: 6500 }
      ]
    },
    features: [
      'Gia công bế khuôn tự động sắc sảo từng mép gập',
      'Có khay định hình nhung / xốp EVA theo yêu cầu',
      'Chịu lực vận chuyển tốt, bảo vệ sản phẩm tuyệt đối'
    ]
  },
  {
    id: 12,
    slug: 'in-lich-de-ban-chu-a-lich-bloc-tet',
    name: 'In Lịch Để Bàn Chữ A - Lịch Bloc Tết',
    categoryId: 'lich-so-tay',
    categoryName: 'Lịch & Quà Tặng',
    tag: 'Quà tặng thương hiệu',
    basePrice: 28000,
    unit: 'cuốn',
    description: 'Lịch để bàn 13 tờ, đế bồi carton bọc xi sang trọng, in thương hiệu doanh nghiệp tri ân khách hàng dịp Tết.',
    image: 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&w=800&q=80',
    turnaround: '3 - 5 ngày',
    minQuantity: 50,
    specs: {
      sizes: [
        { label: 'Lịch Bàn Chữ A (24 x 16 cm - 13 tờ)', value: 'table_a', extra: 0 },
        { label: 'Lịch Bàn Có Ghi Chú Note (24 x 18 cm)', value: 'table_note', extra: 5000 },
        { label: 'Lịch Treo Tường Nẹp Thiếc (40 x 60 cm)', value: 'wall_calendar', extra: 3000 }
      ],
      materials: [
        { label: 'Giấy Couche 250gsm (Láng mịn in ảnh nét)', value: 'c250', extra: 0 },
        { label: 'Giấy Mỹ Thuật vân cát nhập khẩu', value: 'art_paper', extra: 12000 }
      ],
      finishings: [
        { label: 'Đóng lò xo kẽm trắng cao cấp', value: 'spiral', extra: 0 },
        { label: 'Ép kim tên công ty lên chân đế', value: 'foil_base', extra: 3000 }
      ],
      quantities: [
        { label: '50 cuốn', count: 50, pricePerUnit: 45000 },
        { label: '100 cuốn (Khuyên dùng)', count: 100, pricePerUnit: 35000 },
        { label: '300 cuốn', count: 300, pricePerUnit: 29000 },
        { label: '500 cuốn (Giá sỉ xưởng)', count: 500, pricePerUnit: 26000 }
      ]
    },
    features: [
      'Đế lịch cứng cáp đứng vững vàng trên bàn làm việc',
      'Đầy đủ thông tin ngày âm dương và các ngày lễ Tết',
      'Tặng kèm túi nilon quai xách đựng từng cuốn lịch'
    ]
  },
  {
    id: 13,
    slug: 'in-bao-li-xi-tet-doc-quyen',
    name: 'In Bao Lì Xì Tết Doanh Nghiệp',
    categoryId: 'lich-so-tay',
    categoryName: 'Lịch & Quà Tặng',
    tag: 'Độc quyền thiết kế',
    basePrice: 650,
    unit: 'cái',
    description: 'Bao lì xì giấy kraft vintage hoặc couche bóng bẩy, bế dán sẵn ép nhũ vàng sang trọng.',
    image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
    turnaround: '2 - 3 ngày',
    minQuantity: 500,
    specs: {
      sizes: [
        { label: 'Khổ lớn 8 x 16.5 cm (Đựng thẳng tờ 500k)', value: 'std_8x16.5', extra: 0 },
        { label: 'Khổ nhỏ 7.5 x 10 cm (Gấp đôi tờ tiền)', value: 'small_7.5x10', extra: -150 }
      ],
      materials: [
        { label: 'Giấy Couche 150gsm (Màu in rực rỡ, sắc nét)', value: 'c150', extra: 0 },
        { label: 'Giấy Kraft Nhật 120gsm (Phong cách Vintage)', value: 'kraft120', extra: 100 },
        { label: 'Giấy Mỹ Thuật ánh kim cao cấp', value: 'art_shimmer', extra: 450 }
      ],
      finishings: [
        { label: 'Bế dán thành phẩm sẵn', value: 'glue', extra: 0 },
        { label: 'Ép nhũ vàng / đỏ họa tiết Tết', value: 'foil_gold', extra: 350 }
      ],
      quantities: [
        { label: '500 cái', count: 500, pricePerUnit: 1200 },
        { label: '1.000 cái (Phổ biến)', count: 1000, pricePerUnit: 850 },
        { label: '2.000 cái', count: 2000, pricePerUnit: 720 },
        { label: '5.000 cái (Giá xưởng)', count: 5000, pricePerUnit: 580 }
      ]
    },
    features: [
      'Đóng gói sẵn 10 cái/bịch bóng kính tiện phân phát',
      'Chất lượng giấy đanh dày không bị bục mép',
      'Miễn phí thiết kế layout theo yêu cầu nhận diện'
    ]
  }
]

