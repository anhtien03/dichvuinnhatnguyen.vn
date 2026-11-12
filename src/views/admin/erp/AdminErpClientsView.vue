<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Khách Hàng & CRM Zalo
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Danh bạ đối tác, chính sách chiết khấu hoa hồng & đối soát tự động hàng tháng
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          @click="openCreateModal"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 transition-all cursor-pointer"
        >
          <UserPlus class="w-4 h-4" />
          <span>Thêm Khách Hàng Mới</span>
        </button>
      </div>
    </div>

    <!-- Mode Tabs: Danh bạ vs Chăm sóc tự động vs Bảng kê hoa hồng -->
    <div class="flex items-center gap-2 border-b pb-3 overflow-x-auto" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
      <button
        @click="activeTab = 'directory'"
        class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer"
        :class="activeTab === 'directory' ? 'bg-brand-gold text-slate-950 shadow-sm' : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
      >
        <Users class="w-4 h-4" />
        <span>Danh Bạ Khách Hàng</span>
      </button>

      <button
        @click="activeTab = 'care'"
        class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer"
        :class="activeTab === 'care' ? 'bg-brand-gold text-slate-950 shadow-sm' : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
      >
        <HeartHandshake class="w-4 h-4" />
        <span>Chăm Sóc Khách Hàng Tự Động</span>
      </button>

      <button
        @click="switchTab('commission')"
        class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer relative"
        :class="activeTab === 'commission' ? 'bg-brand-gold text-slate-950 shadow-sm' : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
      >
        <Receipt class="w-4 h-4" />
        <span>Bảng Kê Chiết Khấu & Hoa Hồng Tháng</span>
        <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
          Mới
        </span>
      </button>
    </div>

    <!-- TAB 1: DANH BẠ KHÁCH HÀNG -->
    <div v-if="activeTab === 'directory'" class="space-y-4">
      <!-- Search & Tag Filter -->
      <div class="p-4 rounded-2xl border flex flex-col sm:flex-row items-center gap-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="relative flex-1 w-full">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            v-model="searchQuery"
            @keydown.enter="fetchCustomers"
            type="text"
            placeholder="Tìm theo tên khách, số điện thoại, công ty..."
            class="w-full text-xs rounded-xl pl-9 pr-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>

        <div class="w-full sm:w-48">
          <select
            v-model="tagFilter"
            @change="fetchCustomers"
            class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          >
            <option value="">Tất cả phân loại</option>
            <option value="vip">Khách VIP</option>
            <option value="bulk">Khách in nhiều</option>
            <option value="regular">Khách thông thường</option>
            <option value="demanding">Khách khó tính</option>
            <option value="care_needed">Cần chăm sóc gấp</option>
          </select>
        </div>

        <button
          @click="fetchCustomers"
          class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 cursor-pointer"
        >
          Tìm
        </button>
      </div>

      <!-- Customers Table -->
      <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div v-if="isLoading" class="p-12 text-center text-slate-400 text-xs">
          Đang tải danh bạ khách hàng...
        </div>

        <div v-else-if="customers.length === 0" class="p-12 text-center text-slate-400 text-xs">
          Chưa có khách hàng nào.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
              <tr>
                <th class="py-3 px-4">Tên Khách Hàng</th>
                <th class="py-3 px-4">Số Điện Thoại & Zalo</th>
                <th class="py-3 px-4">Chính Sách Chiết Khấu</th>
                <th class="py-3 px-4">Công Ty / MST</th>
                <th class="py-3 px-4">Phân Loại</th>
                <th class="py-3 px-4 text-center">Tổng Đơn In</th>
                <th class="py-3 px-4 text-center">Thao Tác</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
              <tr
                v-for="c in customers"
                :key="c.id"
                class="hover:bg-brand-gold/5 transition-colors"
                :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
              >
                <td class="py-3.5 px-4 font-bold">
                  <div>{{ c.name }}</div>
                  <div class="text-[10px] text-slate-400 font-normal truncate max-w-xs">{{ c.address }}</div>
                </td>

                <td class="py-3.5 px-4 font-mono">
                  <div class="font-bold text-brand-gold">{{ c.phone }}</div>
                  <a
                    :href="`https://zalo.me/${cleanPhone(c.phone)}`"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-[11px] text-blue-400 hover:underline mt-0.5"
                  >
                    <MessageSquare class="w-3 h-3" />
                    <span>Mở Zalo Chat</span>
                  </a>
                </td>

                <td class="py-3.5 px-4">
                  <div v-if="Number(c.commission_rate) > 0" class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                      {{ c.commission_rate }}%
                    </span>
                    <span class="text-[10px] text-slate-400">
                      {{ c.commission_type === 'monthly' ? 'Tích lũy tháng' : 'Trừ từng đơn' }}
                    </span>
                  </div>
                  <div v-else class="text-[10px] text-slate-500 italic">
                    Chưa cài đặt (0%)
                  </div>
                </td>

                <td class="py-3.5 px-4">
                  <div>{{ c.company_name || '-' }}</div>
                  <div class="text-[10px] text-slate-400">MST: {{ c.tax_code || '-' }}</div>
                </td>

                <td class="py-3.5 px-4">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold border"
                    :class="getTagBadge(c.tag)"
                  >
                    {{ getTagLabel(c.tag) }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-center font-bold">
                  {{ c.orders_count || 0 }} đơn
                </td>

                <td class="py-3.5 px-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="editCustomer(c)"
                      class="p-1.5 rounded-lg border text-blue-400 hover:bg-blue-500/10 cursor-pointer"
                      :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                      title="Sửa khách hàng & chính sách chiết khấu"
                    >
                      <Edit3 class="w-4 h-4" />
                    </button>
                    <button
                      @click="deleteCustomer(c)"
                      class="p-1.5 rounded-lg border text-red-400 hover:bg-red-500/10 cursor-pointer"
                      :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                      title="Xóa khách hàng"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: CHĂM SÓC KHÁCH HÀNG TỰ ĐỘNG (DAILY CARE) -->
    <div v-else-if="activeTab === 'care'" class="space-y-4">
      <div class="p-4 rounded-2xl border flex items-center justify-between" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div>
          <h2 class="text-sm font-bold">Danh sách khách hàng lâu chưa in ấn</h2>
          <p class="text-xs text-slate-400">Nhân viên kinh doanh chủ động nhắn tin Zalo chào thăm & báo giá ưu đãi</p>
        </div>

        <div class="flex items-center gap-2">
          <label class="text-xs text-slate-400">Thời gian chưa đặt:</label>
          <select
            v-model="careDays"
            @change="fetchCareList"
            class="text-xs rounded-xl px-3 py-1.5 border font-bold text-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
          >
            <option :value="15">Trên 15 ngày</option>
            <option :value="30">Trên 30 ngày</option>
            <option :value="60">Trên 60 ngày</option>
          </select>
        </div>
      </div>

      <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div v-if="isCareLoading" class="p-12 text-center text-slate-400 text-xs">
          Đang quét dữ liệu chăm sóc khách hàng...
        </div>

        <div v-else-if="careList.length === 0" class="p-12 text-center text-slate-400 text-xs">
          Tuyệt vời! Tất cả khách hàng đều vừa mới đặt đơn trong vòng {{ careDays }} ngày qua.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
              <tr>
                <th class="py-3 px-4">Khách Hàng</th>
                <th class="py-3 px-4">Số Điện Thoại</th>
                <th class="py-3 px-4">Đơn Cuối Cùng</th>
                <th class="py-3 px-4 text-right">Tổng Tiền Đã In</th>
                <th class="py-3 px-4 text-center">Hành Động Chăm Sóc</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
              <tr
                v-for="item in careList"
                :key="item.id"
                class="hover:bg-brand-gold/5 transition-colors"
                :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
              >
                <td class="py-3.5 px-4 font-bold">
                  {{ item.name }}
                  <div class="text-[10px] text-slate-400">{{ item.company_name }}</div>
                </td>

                <td class="py-3.5 px-4 font-mono font-bold text-brand-gold">
                  {{ item.phone }}
                </td>

                <td class="py-3.5 px-4">
                  <span v-if="item.last_order_date" class="text-amber-400 font-semibold">
                    {{ formatDate(item.last_order_date) }}
                  </span>
                  <span v-else class="text-slate-500 italic">
                    Chưa từng đặt đơn
                  </span>
                </td>

                <td class="py-3.5 px-4 text-right font-bold text-emerald-400">
                  {{ formatMoney(item.total_spent) }}
                </td>

                <td class="py-3.5 px-4 text-center">
                  <a
                    :href="`https://zalo.me/${cleanPhone(item.phone)}`"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-blue-600 hover:bg-blue-500 text-white shadow-sm transition-all"
                  >
                    <MessageSquare class="w-3.5 h-3.5" />
                    <span>Nhắn Zalo Ngay</span>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 3: BẢNG KÊ CHIẾT KHẤU & HOA HỒNG THÁNG -->
    <div v-else-if="activeTab === 'commission'" class="space-y-4">
      <!-- Top Filters: Month / Year / Search -->
      <div class="p-4 rounded-2xl border flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <!-- Month / Year Selector -->
        <div class="flex items-center flex-wrap gap-2">
          <div class="flex items-center rounded-xl border p-1" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
            <button
              @click="setCommissionMonth(-1)"
              class="p-1.5 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 transition-colors cursor-pointer"
              title="Tháng trước"
            >
              <ChevronLeft class="w-4 h-4 text-slate-400 hover:text-white" />
            </button>
            <div class="px-2 font-black text-xs text-brand-gold flex items-center gap-1">
              <Calendar class="w-3.5 h-3.5" />
              <span>Tháng {{ commissionMonth }}/{{ commissionYear }}</span>
            </div>
            <button
              @click="setCommissionMonth(1)"
              class="p-1.5 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 transition-colors cursor-pointer"
              title="Tháng sau"
            >
              <ChevronRight class="w-4 h-4 text-slate-400 hover:text-white" />
            </button>
          </div>

          <button
            @click="setThisMonth"
            class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer"
            :class="isThisMonth ? 'bg-brand-gold text-slate-950 border-brand-gold' : (isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#24303F]' : 'border-slate-200 text-slate-600 hover:bg-slate-100')"
          >
            Tháng Này
          </button>

          <!-- Direct Dropdown Selector -->
          <div class="flex items-center gap-1.5">
            <select
              v-model="commissionMonth"
              @change="fetchCommissionStatements"
              class="text-xs rounded-xl px-2.5 py-1.5 border font-semibold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            >
              <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
            </select>
            <select
              v-model="commissionYear"
              @change="fetchCommissionStatements"
              class="text-xs rounded-xl px-2.5 py-1.5 border font-semibold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            >
              <option :value="2025">2025</option>
              <option :value="2026">2026</option>
              <option :value="2027">2027</option>
            </select>
          </div>
        </div>

        <!-- Search input -->
        <div class="relative w-full lg:w-80">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            v-model="commissionSearch"
            @keydown.enter="fetchCommissionStatements"
            type="text"
            placeholder="Tìm theo tên KH, SĐT hoặc công ty..."
            class="w-full text-xs rounded-xl pl-9 pr-8 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
          <button
            v-if="commissionSearch"
            @click="commissionSearch = ''; fetchCommissionStatements()"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="p-4 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
          <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Khách Có Đơn / Cài HH</div>
          <div class="text-xl font-black text-brand-gold mt-1">
            {{ commissionSummary?.total_customers || 0 }} khách
          </div>
          <div class="text-[10px] text-slate-400 mt-0.5">Tổng {{ commissionSummary?.total_orders || 0 }} đơn hoàn thành</div>
        </div>

        <div class="p-4 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
          <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng Doanh Số In (Tạm tính)</div>
          <div class="text-xl font-black text-sky-400 mt-1">
            {{ formatMoney(commissionSummary?.total_subtotal) }}
          </div>
          <div class="text-[10px] text-slate-400 mt-0.5">Cơ sở tính % hoa hồng chuẩn</div>
        </div>

        <div class="p-4 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
          <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng Tiền Bill Khách Trả</div>
          <div class="text-xl font-black text-amber-400 mt-1">
            {{ formatMoney(commissionSummary?.total_billed) }}
          </div>
          <div class="text-[10px] text-slate-400 mt-0.5">Doanh thu thu về sau chiết khấu</div>
        </div>

        <div class="p-4 rounded-2xl border bg-gradient-to-br from-emerald-500/10 to-teal-500/10" :class="isDarkMode ? 'border-emerald-500/30' : 'border-emerald-300'">
          <div class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Tổng Hoa Hồng Tháng Cần Chi</div>
          <div class="text-2xl font-black text-emerald-400 mt-1">
            {{ formatMoney(commissionSummary?.total_commission) }}
          </div>
          <div class="text-[10px] text-slate-400 mt-0.5">Tích lũy chiết khấu trả lại khách</div>
        </div>
      </div>

      <!-- Commission Table -->
      <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div v-if="isCommissionLoading" class="p-12 text-center text-slate-400 text-xs">
          Đang tính toán bảng kê hoa hồng tháng {{ commissionMonth }}/{{ commissionYear }}...
        </div>

        <div v-else-if="commissionStatements.length === 0" class="p-12 text-center text-slate-400 text-xs">
          Không có khách hàng nào phát sinh đơn hàng hoặc cài đặt hoa hồng trong tháng {{ commissionMonth }}/{{ commissionYear }}.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
              <tr>
                <th class="py-3 px-4">Khách Hàng & SĐT</th>
                <th class="py-3 px-4 text-center">Chính Sách</th>
                <th class="py-3 px-4 text-center">Số Đơn</th>
                <th class="py-3 px-4 text-right">Tổng Tiền In (Subtotal)</th>
                <th class="py-3 px-4 text-right">Tiền Khách Trả</th>
                <th class="py-3 px-4 text-right font-black text-emerald-400">Hoa Hồng Tháng Này</th>
                <th class="py-3 px-4 text-center">Thao Tác Đối Soát</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
              <tr
                v-for="item in commissionStatements"
                :key="item.customer_id"
                class="hover:bg-brand-gold/5 transition-colors"
                :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
              >
                <!-- Customer info -->
                <td class="py-3.5 px-4 font-bold">
                  <div class="flex items-center gap-1.5">
                    <span>{{ item.customer_name }}</span>
                    <span
                      v-if="item.tag"
                      class="px-1.5 py-0.2 rounded text-[9px] font-bold border"
                      :class="getTagBadge(item.tag)"
                    >
                      {{ getTagLabel(item.tag) }}
                    </span>
                  </div>
                  <div class="text-[10px] text-slate-400 font-mono font-normal">
                    {{ item.customer_phone }} {{ item.company_name ? '• ' + item.company_name : '' }}
                  </div>
                </td>

                <!-- Policy -->
                <td class="py-3.5 px-4 text-center">
                  <div class="inline-flex flex-col items-center">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black" :class="item.commission_rate > 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400'">
                      {{ item.commission_rate }}%
                    </span>
                    <span class="text-[9px] text-slate-400 mt-0.5">
                      {{ item.commission_type === 'monthly' ? 'Tích lũy tháng' : 'Trừ từng đơn' }}
                    </span>
                  </div>
                </td>

                <!-- Order count -->
                <td class="py-3.5 px-4 text-center font-bold">
                  <button
                    @click="openOrderDetails(item)"
                    class="px-2 py-1 rounded-lg border text-brand-gold hover:bg-brand-gold/10 font-mono transition-colors cursor-pointer"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Bấm để xem danh sách đơn"
                  >
                    {{ item.order_count }} đơn
                  </button>
                </td>

                <!-- Total subtotal -->
                <td class="py-3.5 px-4 text-right font-mono font-bold text-sky-400">
                  {{ formatMoney(item.total_subtotal) }}
                </td>

                <!-- Total billed -->
                <td class="py-3.5 px-4 text-right font-mono">
                  <div class="font-bold">{{ formatMoney(item.total_billed) }}</div>
                  <div v-if="item.direct_discount > 0" class="text-[10px] text-amber-400">
                    Đã trừ ngay: -{{ formatMoney(item.direct_discount) }}
                  </div>
                </td>

                <!-- Monthly commission -->
                <td class="py-3.5 px-4 text-right font-mono font-black text-sm text-emerald-400">
                  {{ formatMoney(item.monthly_commission) }}
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <!-- View order details button -->
                    <button
                      @click="openOrderDetails(item)"
                      class="p-1.5 rounded-lg border text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer"
                      :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                      title="Xem danh sách các đơn trong tháng"
                    >
                      <Eye class="w-4 h-4" />
                    </button>

                    <!-- Copy Zalo message button -->
                    <button
                      @click="copyZaloStatement(item)"
                      class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg font-bold text-[11px] transition-all cursor-pointer"
                      :class="copySuccessId === item.customer_id
                        ? 'bg-emerald-500 text-slate-950 font-black'
                        : 'bg-blue-600 hover:bg-blue-500 text-white'"
                      title="Sao chép bảng kê gửi Zalo cho khách"
                    >
                      <Check v-if="copySuccessId === item.customer_id" class="w-3.5 h-3.5" />
                      <Copy v-else class="w-3.5 h-3.5" />
                      <span>{{ copySuccessId === item.customer_id ? 'Đã chép!' : 'Chép Zalo' }}</span>
                    </button>

                    <!-- Direct Zalo link -->
                    <a
                      :href="`https://zalo.me/${cleanPhone(item.customer_phone)}`"
                      target="_blank"
                      class="p-1.5 rounded-lg border text-blue-400 hover:bg-blue-500/10 cursor-pointer"
                      :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                      title="Mở Zalo nhắn tin ngay"
                    >
                      <MessageSquare class="w-4 h-4" />
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- DETAIL MODAL: XEM DANH SÁCH CÁC ĐƠN CỦA KHÁCH TRONG THÁNG -->
    <div
      v-if="isDetailModalOpen && selectedCustomerDetail"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
    >
      <div
        class="relative w-full max-w-3xl rounded-3xl border shadow-2xl p-6 space-y-4 max-h-[90vh] flex flex-col"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div>
            <div class="flex items-center gap-2">
              <h3 class="text-base font-black text-brand-gold">
                Chi Tiết Đơn Hàng Tháng {{ selectedCustomerDetail.month }}/{{ selectedCustomerDetail.year }}
              </h3>
              <span class="text-xs px-2 py-0.5 rounded-md font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                Chiết khấu: {{ selectedCustomerDetail.commission_rate }}%
              </span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">
              Khách hàng: <span class="font-bold text-white">{{ selectedCustomerDetail.customer_name }}</span> - SĐT: {{ selectedCustomerDetail.customer_phone }}
            </p>
          </div>
          <button @click="isDetailModalOpen = false" class="p-1.5 text-slate-400 hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Orders Table in Modal -->
        <div class="overflow-y-auto flex-1 rounded-xl border" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
          <table class="w-full text-left text-xs">
            <thead class="uppercase font-bold tracking-wider sticky top-0" :class="isDarkMode ? 'bg-[#24303F] text-slate-400' : 'bg-slate-100 text-slate-500'">
              <tr>
                <th class="py-2.5 px-3">Mã Đơn</th>
                <th class="py-2.5 px-3">Ngày Tạo</th>
                <th class="py-2.5 px-3 text-right">Tiền In (Subtotal)</th>
                <th class="py-2.5 px-3 text-right">Khách Trả (Bill)</th>
                <th class="py-2.5 px-3 text-center">Hình Thức</th>
                <th class="py-2.5 px-3 text-right font-black text-emerald-400">Hoa Hồng Đơn</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
              <tr
                v-for="ord in selectedCustomerDetail.orders"
                :key="ord.id"
                class="hover:bg-brand-gold/5"
              >
                <td class="py-2.5 px-3 font-mono font-bold text-brand-gold">
                  #{{ ord.code }}
                </td>
                <td class="py-2.5 px-3 text-slate-400">
                  {{ formatDate(ord.created_at) }}
                </td>
                <td class="py-2.5 px-3 text-right font-mono font-semibold text-sky-400">
                  {{ formatMoney(ord.subtotal) }}
                </td>
                <td class="py-2.5 px-3 text-right font-mono font-semibold">
                  {{ formatMoney(ord.total_amount) }}
                </td>
                <td class="py-2.5 px-3 text-center">
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-bold"
                    :class="ord.discount_mode === 'monthly_commission' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
                  >
                    {{ ord.discount_mode === 'monthly_commission' ? 'Tích lũy tháng' : 'Trừ trực tiếp' }}
                  </span>
                </td>
                <td class="py-2.5 px-3 text-right font-mono font-black text-emerald-400">
                  {{ ord.discount_mode === 'monthly_commission'
                    ? formatMoney(ord.commission_amount || ((ord.subtotal * (selectedCustomerDetail.commission_rate || 0)) / 100))
                    : '0 ₫' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Modal Footer: Summary & Copy Zalo -->
        <div class="pt-3 border-t flex flex-col sm:flex-row items-center justify-between gap-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div class="text-xs space-y-0.5">
            <div>
              <span class="text-slate-400">Tổng doanh số in:</span>
              <span class="font-bold text-sky-400 ml-1.5">{{ formatMoney(selectedCustomerDetail.total_subtotal) }}</span>
            </div>
            <div>
              <span class="text-slate-400">Tổng hoa hồng tháng này:</span>
              <span class="font-black text-emerald-400 ml-1.5 text-sm">{{ formatMoney(selectedCustomerDetail.monthly_commission) }}</span>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="copyZaloStatement(selectedCustomerDetail)"
              class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer"
              :class="copySuccessId === selectedCustomerDetail.customer_id
                ? 'bg-emerald-500 text-slate-950 font-black'
                : 'bg-blue-600 hover:bg-blue-500 text-white'"
            >
              <Check v-if="copySuccessId === selectedCustomerDetail.customer_id" class="w-4 h-4" />
              <Copy v-else class="w-4 h-4" />
              <span>{{ copySuccessId === selectedCustomerDetail.customer_id ? 'Đã sao chép tin nhắn!' : 'Sao chép tin nhắn Zalo' }}</span>
            </button>

            <button
              @click="isDetailModalOpen = false"
              class="px-4 py-2 rounded-xl text-xs font-bold border cursor-pointer"
              :class="isDarkMode ? 'border-[#2E3A47] hover:bg-white/5' : 'border-slate-200 hover:bg-slate-100'"
            >
              Đóng
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div
        class="relative w-full max-w-lg rounded-3xl border shadow-2xl p-6 space-y-4 max-h-[92vh] overflow-y-auto"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
      >
        <button @click="isModalOpen = false" class="absolute top-4 right-4 p-1.5 text-slate-400 hover:text-white cursor-pointer">
          <X class="w-5 h-5" />
        </button>

        <h3 class="text-base font-black text-brand-gold">
          {{ isEditing ? 'Chỉnh Sửa Khách Hàng' : 'Thêm Mới Khách Hàng' }}
        </h3>

        <form @submit.prevent="submitCustomer" class="space-y-3">
          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Tên Khách Hàng (*)</label>
            <input
              v-model="customerForm.name"
              type="text"
              required
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Số Điện Thoại (*)</label>
              <input
                v-model="customerForm.phone"
                type="text"
                required
                class="w-full text-xs rounded-xl px-3 py-2 border font-mono font-bold"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Phân Loại</label>
              <select
                v-model="customerForm.tag"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              >
                <option value="vip">Khách VIP</option>
                <option value="bulk">Khách in nhiều</option>
                <option value="regular">Khách thông thường</option>
                <option value="demanding">Khách khó tính</option>
                <option value="care_needed">Cần chăm sóc gấp</option>
              </select>
            </div>
          </div>

          <!-- Section: Chính Sách Chiết Khấu / Hoa Hồng Khách Hàng -->
          <div class="p-3.5 rounded-2xl border space-y-2.5" :class="isDarkMode ? 'bg-[#24303F]/60 border-[#2E3A47]' : 'bg-amber-50/50 border-amber-200/60'">
            <div class="flex items-center gap-1.5 text-xs font-bold text-brand-gold">
              <Percent class="w-3.5 h-3.5" />
              <span>Chính Sách Chiết Khấu & Hoa Hồng (Đại Lý / CTV)</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] text-slate-400 mb-1">% Chiết khấu / Hoa hồng</label>
                <div class="relative">
                  <input
                    v-model.number="customerForm.commission_rate"
                    type="number"
                    min="0"
                    max="100"
                    step="0.5"
                    placeholder="0"
                    class="w-full text-xs rounded-xl px-3 py-2 border font-bold text-emerald-400"
                    :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
                  />
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                </div>
              </div>

              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Cách thức chiết khấu</label>
                <select
                  v-model="customerForm.commission_type"
                  class="w-full text-xs rounded-xl px-3 py-2 border font-medium"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                >
                  <option value="monthly">Tích lũy tính hoa hồng tháng (Đại lý)</option>
                  <option value="per_order">Trừ trực tiếp trên từng đơn</option>
                </select>
              </div>
            </div>

            <p class="text-[10px] text-slate-400 leading-relaxed">
              💡 <span class="text-brand-gold">Tích lũy tháng:</span> Hóa đơn in ấn khách trả đủ giá tiêu chuẩn. Cuối tháng hệ thống tự tổng kết số tiền hoa hồng cần chiết khấu lại cho khách tại Bảng kê hoa hồng.
            </p>
          </div>

          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Địa Chỉ</label>
            <input
              v-model="customerForm.address"
              type="text"
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Công Ty</label>
              <input
                v-model="customerForm.company_name"
                type="text"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Mã Số Thuế</label>
              <input
                v-model="customerForm.tax_code"
                type="text"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
          </div>

          <div class="pt-3 flex justify-end gap-2 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <button
              type="button"
              @click="isModalOpen = false"
              class="px-4 py-2 rounded-xl text-xs border cursor-pointer"
              :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="px-5 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 cursor-pointer"
            >
              Lưu Khách Hàng
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {
  Users,
  UserPlus,
  HeartHandshake,
  Search,
  MessageSquare,
  Edit3,
  Trash2,
  X,
  Percent,
  Receipt,
  Copy,
  Check,
  Eye,
  Calendar,
  ChevronLeft,
  ChevronRight
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const activeTab = ref('directory')
const customers = ref([])
const careList = ref([])
const isLoading = ref(false)
const isCareLoading = ref(false)
const searchQuery = ref('')
const tagFilter = ref('')
const careDays = ref(15)

// Modal State
const isModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const customerForm = reactive({
  name: '',
  phone: '',
  address: '',
  company_name: '',
  tax_code: '',
  tag: 'regular',
  commission_rate: 0,
  commission_type: 'monthly'
})

// Tab 3: Commission Statement State
const commissionMonth = ref(new Date().getMonth() + 1)
const commissionYear = ref(new Date().getFullYear())
const commissionStatements = ref([])
const commissionSummary = ref(null)
const isCommissionLoading = ref(false)
const commissionSearch = ref('')
const selectedCustomerDetail = ref(null)
const isDetailModalOpen = ref(false)
const copySuccessId = ref(null)

const isThisMonth = computed(() => {
  const now = new Date()
  return commissionMonth.value === (now.getMonth() + 1) && commissionYear.value === now.getFullYear()
})

function switchTab(tab) {
  activeTab.value = tab
  if (tab === 'commission') {
    fetchCommissionStatements()
  }
}

async function fetchCustomers() {
  isLoading.value = true
  try {
    const params = {}
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (tagFilter.value) params.tag = tagFilter.value

    const res = await api.getErpCustomers(params)
    if (res.data) {
      customers.value = res.data.data || []
    }
  } catch (err) {
    console.error('Failed to load customers', err)
  } finally {
    isLoading.value = false
  }
}

async function fetchCareList() {
  isCareLoading.value = true
  try {
    const res = await api.getErpDailyCare(careDays.value)
    if (res.data) {
      careList.value = res.data
    }
  } catch (err) {
    console.error('Failed to load care list', err)
  } finally {
    isCareLoading.value = false
  }
}

async function fetchCommissionStatements() {
  isCommissionLoading.value = true
  try {
    const params = {
      month: commissionMonth.value,
      year: commissionYear.value
    }
    if (commissionSearch.value.trim()) {
      params.search = commissionSearch.value.trim()
    }
    const res = await api.getErpCustomerCommissionStatement(params)
    if (res.data) {
      commissionStatements.value = res.data
      commissionSummary.value = res.summary
    }
  } catch (err) {
    console.error('Failed to fetch commission statements', err)
  } finally {
    isCommissionLoading.value = false
  }
}

function setCommissionMonth(delta) {
  let m = commissionMonth.value + delta
  let y = commissionYear.value
  if (m > 12) {
    m = 1
    y += 1
  } else if (m < 1) {
    m = 12
    y -= 1
  }
  commissionMonth.value = m
  commissionYear.value = y
  fetchCommissionStatements()
}

function setThisMonth() {
  const now = new Date()
  commissionMonth.value = now.getMonth() + 1
  commissionYear.value = now.getFullYear()
  fetchCommissionStatements()
}

function openOrderDetails(item) {
  selectedCustomerDetail.value = item
  isDetailModalOpen.value = true
}

function copyZaloStatement(item) {
  const text = `*BẢNG KÊ CHIẾT KHẤU & HOA HỒNG THÁNG ${item.month}/${item.year}*
Kính gửi: ${item.customer_name}${item.company_name ? ' - ' + item.company_name : ''}
Số điện thoại: ${item.customer_phone}
---------------------------------
- Số lượng đơn hoàn thành: ${item.order_count} đơn
- Tổng doanh số in: ${formatMoney(item.total_subtotal)}
- Mức chiết khấu / hoa hồng: ${item.commission_rate}%
- TỔNG HOA HỒNG THÁNG NÀY: ${formatMoney(item.monthly_commission)}
---------------------------------
Anh/chị vui lòng kiểm tra và gửi lại số tài khoản để In Ấn Nhật Nguyên chuyển khoản đối soát nhé. Trân trọng cảm ơn!`

  navigator.clipboard.writeText(text).then(() => {
    copySuccessId.value = item.customer_id
    setTimeout(() => {
      if (copySuccessId.value === item.customer_id) copySuccessId.value = null
    }, 2500)
  })
}

function openCreateModal() {
  isEditing.value = false
  editingId.value = null
  customerForm.name = ''
  customerForm.phone = ''
  customerForm.address = ''
  customerForm.company_name = ''
  customerForm.tax_code = ''
  customerForm.tag = 'regular'
  customerForm.commission_rate = 0
  customerForm.commission_type = 'monthly'
  isModalOpen.value = true
}

function editCustomer(c) {
  isEditing.value = true
  editingId.value = c.id
  customerForm.name = c.name
  customerForm.phone = c.phone
  customerForm.address = c.address || ''
  customerForm.company_name = c.company_name || ''
  customerForm.tax_code = c.tax_code || ''
  customerForm.tag = c.tag || 'regular'
  customerForm.commission_rate = Number(c.commission_rate) || 0
  customerForm.commission_type = c.commission_type || 'monthly'
  isModalOpen.value = true
}

async function submitCustomer() {
  try {
    if (isEditing.value) {
      await api.updateErpCustomer(editingId.value, customerForm)
    } else {
      await api.createErpCustomer(customerForm)
    }
    isModalOpen.value = false
    fetchCustomers()
    if (activeTab.value === 'commission') {
      fetchCommissionStatements()
    }
  } catch (err) {
    alert(err.message || 'Lỗi khi lưu khách hàng')
  }
}

async function deleteCustomer(c) {
  if (confirm(`Bạn có chắc muốn xóa khách hàng [${c.name}]?`)) {
    try {
      await api.deleteErpCustomer(c.id)
      fetchCustomers()
      if (activeTab.value === 'commission') {
        fetchCommissionStatements()
      }
    } catch (err) {
      alert(err.message || 'Lỗi khi xóa')
    }
  }
}

function cleanPhone(p) {
  return (p || '').replace(/\D/g, '')
}

function formatMoney(num) {
  if (!num) return '0 ₫'
  return new Intl.NumberFormat('vi-VN').format(Math.round(num)) + ' ₫'
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`
}

function getTagLabel(tag) {
  switch (tag) {
    case 'vip': return 'Khách VIP'
    case 'bulk': return 'In nhiều'
    case 'demanding': return 'Khó tính'
    case 'care_needed': return 'Cần chăm sóc'
    default: return 'Thông thường'
  }
}

function getTagBadge(tag) {
  switch (tag) {
    case 'vip': return 'bg-amber-500/10 text-amber-400 border-amber-500/20'
    case 'bulk': return 'bg-blue-500/10 text-blue-400 border-blue-500/20'
    case 'demanding': return 'bg-red-500/10 text-red-400 border-red-500/20'
    case 'care_needed': return 'bg-purple-500/10 text-purple-400 border-purple-500/20'
    default: return 'bg-slate-500/10 text-slate-400 border-slate-500/20'
  }
}

onMounted(() => {
  fetchCustomers()
  fetchCareList()
})
</script>
