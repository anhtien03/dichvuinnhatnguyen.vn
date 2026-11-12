// src/data/megaMenuData.js
// Cấu trúc Mega Menu phong cách TheGioiInAn chuẩn cho Xưởng In Nhật Nguyên

export const megaMenuData = [
  {
    id: 'an-pham-tet',
    title: 'Ấn Phẩm Tết',
    badge: 'Mới',
    badgeColor: 'bg-amber-400 text-slate-950 font-black',
    slug: 'lich-so-tay',
    featured: {
      title: 'BAO LÌ XÌ ÉP KIM ĐỘC QUYỀN',
      subtitle: 'Thiết kế sang trọng, giấy mỹ thuật & ánh kim đón xuân may mắn',
      image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
      actionText: 'Đặt In Bao Lì Xì Tết',
      productName: 'Bao Lì Xì Tết Ép Kim'
    },
    columns: [
      {
        heading: 'Bao Lì Xì Doanh Nghiệp',
        items: [
          { name: 'Bao lì xì ép kim cao cấp', badge: 'Hot', hasDetail: false },
          { name: 'Bao lì xì giấy Kraft vintage', badge: 'Mới', hasDetail: false },
          { name: 'Bao lì xì giấy Couche in offset 4 màu', badge: 'In nhanh', hasDetail: false },
          { name: 'Bao lì xì giấy Mỹ thuật sang trọng', hasDetail: false },
          { name: 'Bao lì xì dập nổi / dập chìm logo', hasDetail: false }
        ]
      },
      {
        heading: 'Lịch Tết Quà Tặng',
        items: [
          { name: 'Lịch để bàn chữ A chuẩn', badge: 'Bán chạy', hasDetail: false },
          { name: 'Lịch để bàn chữ M có note ghi chú', hasDetail: false },
          { name: 'Lịch treo tường 7 tờ lò xo', hasDetail: false },
          { name: 'Lịch Bloc đại đặc biệt cao cấp', hasDetail: false },
          { name: 'Lịch nẹp thiếc 1 tờ giá rẻ', hasDetail: false }
        ]
      },
      {
        heading: 'Hộp Quà & Túi Đựng Tết',
        items: [
          { name: 'Hộp quà Tết nắp nam châm cao cấp', hasDetail: false },
          { name: 'Túi giấy đựng quà Tết ép kim', hasDetail: false },
          { name: 'Hộp đựng yến sào & đông trùng Tết', hasDetail: false },
          { name: 'Tag treo chúc mừng năm mới', hasDetail: false }
        ]
      }
    ]
  },
  {
    id: 'van-phong',
    title: 'Văn Phòng',
    badge: '',
    slug: 'an-pham-van-phong',
    featured: {
      title: 'DANH THIẾP NGỌC TRAI ÁNH KIM',
      subtitle: 'Chất liệu giấy ngọc trai cao cấp, ánh kim sang trọng đẳng cấp doanh nhân',
      image: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=800&q=80',
      actionText: 'Xem Mẫu Danh Thiếp',
      slug: 'in-danh-thiep-namecard',
      hasDetail: true,
      productName: 'Danh thiếp ngọc trai'
    },
    columns: [
      {
        heading: 'Danh thiếp - Namecards',
        items: [
          { name: 'In Danh Thiếp - Namecard Chuẩn', slug: 'in-danh-thiep-namecard', hasDetail: true, badge: 'Giá xưởng' },
          { name: 'Danh thiếp Giấy mỹ thuật', badge: 'In nhanh', hasDetail: false },
          { name: 'Danh thiếp Ngọc Trai ánh kim', badge: 'Hot', hasDetail: false },
          { name: 'Danh thiếp Kỹ thuật số lấy liền 2H', badge: 'In nhanh', hasDetail: false },
          { name: 'Danh thiếp Thông minh NFC / QR Code', hasDetail: false },
          { name: 'Danh thiếp Ép kim / Dập nổi logo', hasDetail: false },
          { name: 'Danh thiếp Giấy nhựa xé không rách', hasDetail: false },
          { name: 'Danh thiếp Bo góc tròn tinh tế', hasDetail: false },
          { name: 'Danh thiếp Gấp đôi độc đáo', hasDetail: false },
          { name: 'Danh thiếp Không cán màng viết được', hasDetail: false }
        ]
      },
      {
        heading: 'Bao thư - Envelopes',
        items: [
          { name: 'In Bao Thư - Phong Bì Đầy Đủ Size', slug: 'in-bao-thu-phong-bi', hasDetail: true, badge: 'Bán chạy' },
          { name: 'Bao thư nhỏ 12x22cm lấy ngay', badge: 'In nhanh', hasDetail: false },
          { name: 'Bao thư trung 16x23cm (A5)', hasDetail: false },
          { name: 'Bao thư lớn 25x35cm đựng hồ sơ A4', hasDetail: false },
          { name: 'Bao thư giấy Kraft tự nhiên', hasDetail: false },
          { name: 'Bao thư có cửa sổ kính trong suốt', hasDetail: false }
        ],
        subheading: 'Đồng Phục & Áo Thun',
        subitems: [
          { name: 'Áo thun đồng phục cổ bẻ (Polo)', hasDetail: false },
          { name: 'Áo thun đồng phục cổ tròn teambuilding', hasDetail: false }
        ]
      },
      {
        heading: 'Tài Liệu & Bìa Hồ Sơ',
        items: [
          { name: 'Bìa đựng hồ sơ Folder 1 tay gấp', hasDetail: false },
          { name: 'Bìa đựng hồ sơ Folder 2 tay gấp có khe namecard', hasDetail: false },
          { name: 'Bìa đựng hồ sơ cao cấp cán màng mờ', hasDetail: false },
          { name: 'Giấy tiêu đề thư công ty (Letterheads)', badge: 'In nhanh', hasDetail: false },
          { name: 'Giấy ghi chú - Block notes văn phòng', hasDetail: false },
          { name: 'Phiếu thu chi, hóa đơn bán lẻ 2-3 liên', hasDetail: false }
        ]
      }
    ]
  },
  {
    id: 'tiep-thi',
    title: 'Tiếp Thị',
    badge: '',
    slug: 'tiep-thi-quang-cao',
    featured: {
      title: 'TỜ RƠI & BROCHURE QUẢNG CÁO',
      subtitle: 'In offset 4 màu sắc nét chuẩn hệ màu Châu Âu, tiếp cận hàng ngàn khách hàng tiềm năng',
      image: 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
      actionText: 'Đặt In Tờ Rơi Giá Xưởng',
      slug: 'in-to-roi-brochure',
      hasDetail: true,
      productName: 'Tờ rơi brochure'
    },
    columns: [
      {
        heading: 'Tờ Rơi & Tờ Gấp (Flyer/Brochure)',
        items: [
          { name: 'In Tờ Rơi - Flyer - Brochure', slug: 'in-to-roi-brochure', hasDetail: true, badge: 'Giá gốc' },
          { name: 'Tờ gấp đôi, gấp ba (Brochure A4)', badge: 'In nhanh', hasDetail: false },
          { name: 'Tờ rơi A5 Couche 150gsm thông dụng', badge: 'Hot', hasDetail: false },
          { name: 'Tờ rơi A4 Couche 200gsm cao cấp', hasDetail: false },
          { name: 'Tờ rơi in nhanh kỹ thuật số số lượng ít', badge: 'Lấy ngay', hasDetail: false }
        ]
      },
      {
        heading: 'Catalogue & Profile Doanh Nghiệp',
        items: [
          { name: 'Catalogue bấm kim giữa chuẩn', hasDetail: false },
          { name: 'Catalogue đóng gáy keo nhiệt dày', hasDetail: false },
          { name: 'Profile / Hồ sơ năng lực đấu thầu', hasDetail: false },
          { name: 'Kỷ yếu doanh nghiệp & hội khóa', hasDetail: false }
        ]
      },
      {
        heading: 'Menu & Phiếu Quà Tặng',
        items: [
          { name: 'Menu bìa cứng bồi carton chống gãy', hasDetail: false },
          { name: 'Menu ép plastic dẻo chống nước 100%', hasDetail: false },
          { name: 'Menu dạng cuốn lò xo đóng ốc', hasDetail: false },
          { name: 'Voucher - Phiếu giảm giá quà tặng', badge: 'In nhanh', hasDetail: false },
          { name: 'Vé vào cổng, phiếu bốc thăm trúng thưởng', hasDetail: false }
        ]
      }
    ]
  },
  {
    id: 'bao-bi',
    title: 'Bao Bì',
    badge: '',
    slug: 'bao-bi-tui-giay',
    featured: {
      title: 'HỘP CỨNG & TÚI GIẤY CAO CẤP',
      subtitle: 'Nâng tầm giá trị sản phẩm với kỹ thuật ép kim, cán gân, dập nổi trực tiếp tại xưởng',
      image: 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?auto=format&fit=crop&w=800&q=80',
      actionText: 'Tư Vấn Thiết Kế Hộp & Túi',
      slug: 'in-hop-giay-hop-cung',
      hasDetail: true,
      productName: 'Hộp giấy cao cấp'
    },
    columns: [
      {
        heading: 'Túi Giấy Bán Hàng & Quà Tặng',
        items: [
          { name: 'In Túi Giấy Cao Cấp Đựng Quà', slug: 'in-tui-giay-cao-cap', hasDetail: true, badge: 'Bán chạy' },
          { name: 'Túi giấy Kraft nâu thân thiện môi trường', hasDetail: false },
          { name: 'Túi giấy shop thời trang quai dù', badge: 'Hot', hasDetail: false },
          { name: 'Túi giấy đựng mỹ phẩm, trang sức mini', hasDetail: false },
          { name: 'Túi giấy đựng bánh mì, thực phẩm an toàn', hasDetail: false }
        ]
      },
      {
        heading: 'Hộp Giấy & Hộp Cứng',
        items: [
          { name: 'In Hộp Giấy - Hộp Cứng Cao Cấp', slug: 'in-hop-giay-hop-cung', hasDetail: true, badge: 'Giá xưởng' },
          { name: 'Hộp carton sóng E đóng gói ship COD', hasDetail: false },
          { name: 'Hộp giấy Duplex / Ivory bế cửa sổ bóng kính', hasDetail: false },
          { name: 'Hộp nắp âm dương cao cấp đựng yến, sâm', hasDetail: false },
          { name: 'Hộp bánh sinh nhật, bánh ngọt có quai xách', hasDetail: false }
        ]
      },
      {
        heading: 'Phụ Kiện Đóng Gói',
        items: [
          { name: 'Giấy lót gói hàng in logo (Giấy nến/Pelure)', hasDetail: false },
          { name: 'Băng keo in thương hiệu dán thùng', hasDetail: false },
          { name: 'Thẻ tag cảm ơn đính kèm đơn hàng (Thank You Card)', badge: 'In nhanh', hasDetail: false }
        ]
      }
    ]
  },
  {
    id: 'tem-nhan',
    title: 'Tem Nhãn',
    badge: 'Chống nước',
    badgeColor: 'bg-emerald-400 text-slate-950 font-black',
    slug: 'tem-nhan-decal',
    featured: {
      title: 'TEM NHÃN DECAL BẾ DEMI THEO KHUÔN',
      subtitle: 'Chống thấm nước tuyệt đối, bế demi mọi hình dạng tròn, elip, theo viền logo sắc nét',
      image: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?auto=format&fit=crop&w=800&q=80',
      actionText: 'Báo Giá Tem Decal Bế Sẵn',
      slug: 'in-tem-nhan-decal',
      hasDetail: true,
      productName: 'Tem nhãn decal'
    },
    columns: [
      {
        heading: 'Decal Nhãn Sản Phẩm',
        items: [
          { name: 'In Tem Nhãn Decal - Sticker Bế Sẵn', slug: 'in-tem-nhan-decal', hasDetail: true, badge: 'In nhanh' },
          { name: 'Decal giấy cán bóng / cán mờ giá rẻ', hasDetail: false },
          { name: 'Decal nhựa PVC trắng sữa chống nước', badge: 'Hot', hasDetail: false },
          { name: 'Decal trong suốt dán chai lọ trà sữa, mỹ phẩm', hasDetail: false },
          { name: 'Decal Kraft nâu phong cách handmade mộc', hasDetail: false }
        ]
      },
      {
        heading: 'Tem Bảo Mật & Kỹ Thuật',
        items: [
          { name: 'Tem bảo hành vỡ (tem bể giòn dán linh kiện)', badge: 'Bán chạy', hasDetail: false },
          { name: 'Tem xi bạc (decal thiếc kim loại chịu nhiệt)', hasDetail: false },
          { name: 'Tem Hologram 7 màu chống làm giả', hasDetail: false },
          { name: 'Tem niêm phong bóc để lại chữ VOID / Open', hasDetail: false }
        ]
      },
      {
        heading: 'Tag Treo - Thẻ Bài Quần Áo',
        items: [
          { name: 'In Tag Treo - Nhãn Mác Quần Áo', slug: 'in-tag-treo-nhan-mac', hasDetail: true, badge: 'Giá gốc' },
          { name: 'Tag treo tròn, hình chuông, bo góc', hasDetail: false },
          { name: 'Mác dệt, mác in satin gắn cổ áo', hasDetail: false },
          { name: 'Dây treo tag đạn nhựa, dây dù xỏ', hasDetail: false }
        ]
      }
    ]
  },
  {
    id: 'su-kien',
    title: 'Sự Kiện',
    badge: '',
    slug: 'su-kien-trien-lam',
    featured: {
      title: 'STANDEE & BACKDROP HỘI NGHỊ',
      subtitle: 'Thiết bị trưng bày quảng cáo hội thảo, triển lãm, khai trương thi công thần tốc',
      image: 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=800&q=80',
      actionText: 'Đặt In Standee Lấy Ngay',
      productName: 'Standee sự kiện'
    },
    columns: [
      {
        heading: 'Standee Trưng Bày',
        items: [
          { name: 'Standee chân chữ X cường lực (0.6x1.6m, 0.8x1.8m)', badge: 'Lấy ngay', hasDetail: false },
          { name: 'Standee cuốn nhôm cao cấp chống gió', badge: 'Bán chạy', hasDetail: false },
          { name: 'Standee đế nước cản gió ngoài trời', hasDetail: false },
          { name: 'Khung poster chữ A hai mặt', hasDetail: false }
        ]
      },
      {
        heading: 'Băng Rôn & Backdrop',
        items: [
          { name: 'Băng rôn khai trương, khuyến mãi Hiflex', hasDetail: false },
          { name: 'Backdrop sân khấu hội nghị bạt chống xuyên sáng', hasDetail: false },
          { name: 'Poster PP trong nhà cán màng mờ', hasDetail: false },
          { name: 'Hashtag check-in chụp hình sự kiện cầm tay', badge: 'Hot', hasDetail: false }
        ]
      },
      {
        heading: 'Phụ Kiện Sự Kiện',
        items: [
          { name: 'Thẻ đeo ban tổ chức & đại biểu hội nghị', hasDetail: false },
          { name: 'Dây đeo thẻ in logo sắc nét', hasDetail: false },
          { name: 'Vòng tay giấy dùng 1 lần kiểm soát ra vào', hasDetail: false },
          { name: 'Quạt nhựa quảng cáo in logo', hasDetail: false }
        ]
      }
    ]
  },
  {
    id: 'qua-tang',
    title: 'Quà Tặng',
    badge: '',
    slug: 'lich-so-tay',
    featured: {
      title: 'SỔ TAY BÌA DA & BÚT KÝ KHẮC LOGO',
      subtitle: 'Món quà tri ân tinh tế khẳng định sự chuyên nghiệp và đẳng cấp của doanh nghiệp bạn',
      image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
      actionText: 'Tư Vấn Quà Tặng Doanh Nghiệp',
      productName: 'Sổ tay quà tặng'
    },
    columns: [
      {
        heading: 'Sổ Tay Doanh Nghiệp',
        items: [
          { name: 'Sổ tay bìa da PU còng 6 lỗ cao cấp', badge: 'Hot', hasDetail: false },
          { name: 'Sổ tay bìa da dán gáy sang trọng', hasDetail: false },
          { name: 'Sổ tay lò xo bìa cứng ghi chép nhanh', hasDetail: false },
          { name: 'Sổ tay bìa gỗ khắc laser độc bản', hasDetail: false }
        ]
      },
      {
        heading: 'Vật Phẩm Khắc Logo',
        items: [
          { name: 'Bút kim loại cao cấp khắc tên/logo', badge: 'In nhanh', hasDetail: false },
          { name: 'Bình giữ nhiệt inox 304 in logo xưởng', hasDetail: false },
          { name: 'USB kim loại / USB gỗ in thương hiệu', hasDetail: false },
          { name: 'Đồng hồ treo tường quà tặng tri ân', hasDetail: false }
        ]
      },
      {
        heading: 'Ly Sứ & Kỷ Niệm Chương',
        items: [
          { name: 'Ly sứ trắng in hình ảnh sắc nét', hasDetail: false },
          { name: 'Kỷ niệm chương pha lê vinh danh', hasDetail: false },
          { name: 'Huy hiệu kim loại, bảng tên cài áo nhân viên', hasDetail: false }
        ]
      }
    ]
  },
  {
    id: 'in-nhanh',
    title: 'In Nhanh',
    badge: '2 Giờ',
    badgeColor: 'bg-red-500 text-white font-black animate-pulse',
    slug: 'an-pham-van-phong',
    featured: {
      title: 'DỊCH VỤ IN NHANH KỸ THUẬT SỐ 2H',
      subtitle: 'Đáp ứng các đơn hàng gấp dự hội thảo, công tác, khai trương trong ngày với chất lượng chuẩn nét',
      image: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=800&q=80',
      actionText: 'Báo Giá In Gấp Trong 15 Phút',
      productName: 'In nhanh lấy liền 2H'
    },
    columns: [
      {
        heading: 'Lấy Ngay Trong Ngày (2H - 4H)',
        items: [
          { name: 'In Danh Thiếp lấy ngay sau 2 giờ', slug: 'in-danh-thiep-namecard', hasDetail: true, badge: 'Gấp 2H' },
          { name: 'In Tờ Rơi số lượng ít lấy trong ngày', slug: 'in-to-roi-brochure', hasDetail: true, badge: 'Lấy liền' },
          { name: 'In Tem Nhãn Decal bế demi lấy ngay', slug: 'in-tem-nhan-decal', hasDetail: true, badge: 'Trong ngày' },
          { name: 'In Bao Thư số lượng ít kỹ thuật số', slug: 'in-bao-thu-phong-bi', hasDetail: true, badge: 'Gấp' }
        ]
      },
      {
        heading: 'In Tài Liệu & Luận Văn',
        items: [
          { name: 'In tài liệu màu hội thảo đóng gáy xoắn', hasDetail: false },
          { name: 'In hồ sơ thầu, bản vẽ kiến trúc khổ lớn', hasDetail: false },
          { name: 'Đóng bìa cứng mạ vàng luận văn tốt nghiệp', hasDetail: false }
        ]
      },
      {
        heading: 'In Mẫu & Proofing Test Màu',
        items: [
          { name: 'In test proof chuẩn màu máy in offset', badge: 'Chuẩn màu', hasDetail: false },
          { name: 'Gia công mẫu hộp / túi giấy 1 chiếc duyệt trước', hasDetail: false },
          { name: 'Kỹ thuật viên hỗ trợ kiểm tra file in miễn phí', hasDetail: false }
        ]
      }
    ]
  }
]

