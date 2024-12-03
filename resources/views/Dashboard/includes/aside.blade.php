  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{auth('user')->user()->getImage()}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{auth('user')->user()->name}}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{route('dashboard.home')}}" class="nav-link {{request()->is('*/dashboard')? 'active':''}}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                {{ trans('admin.Dashboard') }}
              </p>
            </a>
          </li>

          {{-- menu-open --}}
          @if (auth('user')->user()->has_permission('read-users'))
            <li class="nav-item {{(request()->routeIs('dashboard.users.*') || request()->routeIs('dashboard.roles.*'))? 'menu-open':''}}">
              <a href="#" class="nav-link">
                <i class="fas fa-user"></i>
                <p>
                  {{ trans('admin.Users Mangement') }}
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('dashboard.users.index')}}" class="nav-link {{(request()->routeIs('dashboard.users.*'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Users') }}</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{route('dashboard.roles.index')}}" class="nav-link {{( request()->routeIs('dashboard.roles.*'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Roles') }} </p>
                  </a>
                </li>
              </ul>
            </li>
          @endif

          @if (auth('user')->user()->has_permission('read-contacts'))
            <li class="nav-item {{(request()->routeIs('dashboard.contacts.*'))? 'menu-open':''}}">
              <a href="#" class="nav-link">
                <i class="fas fa-user"></i>
                <p>
                  {{ trans('admin.contacts Mangement') }}
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('dashboard.contacts.index')}}" class="nav-link {{(request()->routeIs('dashboard.contacts.*') && Request()->type == null)? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.all contacts') }}</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{route('dashboard.contacts.index')}}?type=customer" class="nav-link {{(request()->routeIs('dashboard.contacts.*') && Request()->type == 'customer')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.customers') }}</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{route('dashboard.contacts.index')}}?type=supplier" class="nav-link {{(request()->routeIs('dashboard.contacts.*') && Request()->type == 'supplier')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.suppliers') }}</p>
                  </a>
                </li>
                @if (auth('user')->user()->has_permission('import-contacts'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.contacts.importContactsView')}}" class="nav-link {{(request()->routeIs('dashboard.contacts.importContactsView'))? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.import-contacts') }}</p>
                    </a>
                  </li>
                @endif

              </ul>
            </li>
          @endif

          @if (auth('user')->user()->has_permission('read-cities') || auth('user')->user()->has_permission('read-governorates'))
            <li class="nav-item {{(request()->routeIs('dashboard.governorates.*'))? 'menu-open':''}} {{(request()->routeIs('dashboard.cities.*'))? 'menu-open':''}}">
              <a href="#" class="nav-link">
                <i class="fas fa-user"></i>
                <p>
                  {{ trans('admin.cities') }} & {{ trans('admin.governorates') }}
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @if (auth('user')->user()->has_permission('read-governorates'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.governorates.index')}}" class="nav-link {{(request()->routeIs('dashboard.governorates.*') && Request()->type == null)? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.governorates') }}</p>
                    </a>
                  </li>
                @endif
                @if (auth('user')->user()->has_permission('read-cities'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.cities.index')}}" class="nav-link {{(request()->routeIs('dashboard.cities.*') && Request()->type == 'customer')? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.cities') }}</p>
                    </a>
                  </li>
                @endif
               

              </ul>
            </li>
          @endif

          @if (auth('user')->user()->has_permission('read-sells'))
          <li class="nav-item {{(request()->routeIs('dashboard.sells.*'))? 'menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="fas fa-user"></i>
              <p>
                {{ trans('admin.sells') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if (auth('user')->user()->has_permission('read-sells'))
                <li class="nav-item">
                  <a href="{{route('dashboard.sells.index')}}" class="nav-link {{(request()->routeIs('dashboard.sells.index'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.sells') }}</p>
                  </a>
                </li>
              @endif

              @if (auth('user')->user()->has_permission('create-sells'))
                <li class="nav-item">
                  <a href="{{route('dashboard.sells.create')}}" class="nav-link {{(request()->routeIs('dashboard.sells.create'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.add sells') }}</p>
                  </a>
                </li>
              @endif

              @if (auth('user')->user()->has_permission('read-sell-drafts'))
                <li class="nav-item">
                  <a href="{{route('dashboard.sells.drafts.index')}}" class="nav-link {{(request()->routeIs('dashboard.sells.drafts.index'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.drafts') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('read-sell-return'))
                <li class="nav-item">
                  <a href="{{route('dashboard.sells.sell-return.index')}}" class="nav-link {{(request()->routeIs('dashboard.sells.sell-return.index'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.sell-return') }}</p>
                  </a>
                </li>
              @endif
            </ul>
          </li>
        @endif

        @if (auth('user')->user()->has_permission('read-purchases'))
          <li class="nav-item {{(request()->routeIs('dashboard.purchases.*'))? 'menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="fas fa-shopping-cart"></i>
              <p>
                {{ trans('admin.purchases') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('dashboard.purchases.index')}}" class="nav-link {{(request()->routeIs('dashboard.purchases.index'))? 'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ trans('admin.purchasesList') }}</p>
                </a>
              </li>

              @if (auth('user')->user()->has_permission('create-purchases'))
                <li class="nav-item">
                  <a href="{{route('dashboard.purchases.create')}}" class="nav-link {{(request()->routeIs('dashboard.purchases.create'))? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.createpurchases') }}</p>
                  </a>
                </li>
              @endif

              <li class="nav-item">
                <a href="{{route('dashboard.purchases.purchase-return.index')}}" class="nav-link {{(request()->routeIs('dashboard.purchases.purchase-return.index'))? 'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ trans('admin.purchase-return') }}</p>
                </a>
              </li>
            </ul>
          </li>
        @endif

          @if (auth('user')->user()->has_permission('read-products') || 
              auth('user')->user()->has_permission('read-categories')||
              auth('user')->user()->has_permission('read-units')||
              auth('user')->user()->has_permission('read-brands')||
              auth('user')->user()->has_permission('read-branchs')||
              auth('user')->user()->has_permission('read-activityTypes')||
              auth('user')->user()->has_permission('read-import-products')||
              auth('user')->user()->has_permission('read-import-open-stock')||
              auth('user')->user()->has_permission('read-sales-segments'))
            <li class="nav-item {{(request()->routeIs('dashboard.products.*') ||
                                  request()->routeIs('dashboard.categories.*')||
                                  request()->routeIs('dashboard.units.*')||
                                  request()->routeIs('dashboard.brands.*')||
                                  request()->routeIs('dashboard.branchs.*')||
                                  request()->routeIs('dashboard.sales-segments.*') ||

                                  request()->routeIs('dashboard.activityTypes.*'))? 'menu-open':''}}">
              <a href="#" class="nav-link">
                <i class="fas fa-user"></i>
                <p>
                  {{ trans('admin.Product Mangement') }}
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                
                @if (auth('user')->user()->has_permission('read-products') || auth('user')->user()->has_permission('import-products') || auth('user')->user()->has_permission('import-open-stock'))
                  <li class="nav-item {{(request()->routeIs('dashboard.products.*') || request()->routeIs('dashboard.import-products') || request()->routeIs('dashboard.import-open-stock'))? 'menu-open':''}}">
                    <a href="#" class="nav-link">
                      <i class="fas fa-shopping-cart"></i>
                      <p>
                        {{ trans('admin.products') }}
                        <i class="right fas fa-angle-left"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      @if (auth('user')->user()->has_permission('read-products'))
                        <li class="nav-item">
                          <a href="{{route('dashboard.products.index')}}" class="nav-link {{(request()->routeIs('dashboard.products.*'))? 'active':''}}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{ trans('admin.products') }}</p>
                          </a>
                        </li>
                      @endif
                      @if (auth('user')->user()->has_permission('import-products'))
                        <li class="nav-item">
                          <a href="{{route('dashboard.products.importProductsView')}}" class="nav-link {{(request()->routeIs('dashboard.import-products.*'))? 'active':''}}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{ trans('admin.import-products') }}</p>
                          </a>
                        </li>
                      @endif
                      @if (auth('user')->user()->has_permission('import-open-stock'))
                          <li class="nav-item">
                            <a href="{{route('dashboard.products.importOpenStockView')}}" class="nav-link {{(request()->routeIs('dashboard.import-open-stock.*'))? 'active':''}}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{ trans('admin.import-open-stock') }}</p>
                          </a>
                        </li>
                      @endif
                    </ul>
                  </li>
                @endif

                @if (auth('user')->user()->has_permission('read-categories'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.categories.index')}}" class="nav-link {{(request()->routeIs('dashboard.categories.*'))? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.Categories') }}</p>
                    </a>
                  </li>
                @endif

                @if (auth('user')->user()->has_permission('read-units'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.units.index')}}" class="nav-link {{(request()->routeIs('dashboard.units.*'))? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.units') }}</p>
                    </a>
                  </li>
                @endif
                
                @if (auth('user')->user()->has_permission('read-brands'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.brands.index')}}" class="nav-link {{(request()->routeIs('dashboard.brands.*'))? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.brands') }}</p>
                    </a>
                  </li>
                @endif

                @if (auth('user')->user()->has_permission('read-activityTypes'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.activityTypes.index')}}" class="nav-link {{(request()->routeIs('dashboard.activityTypes.*'))? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.activityTypes') }}</p>
                    </a>
                  </li>
                @endif
                @if (auth('user')->user()->has_permission('read-branchs'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.branchs.index')}}" class="nav-link {{(request()->routeIs('dashboard.branchs.*'))? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.branchs') }}</p>
                    </a>
                  </li>
                @endif

                @if (auth('user')->user()->has_permission('read-sales-segments'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.sales-segments.index')}}" class="nav-link {{(request()->routeIs('dashboard.sales-segments.*'))? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.sales_segments') }}</p>
                    </a>
                  </li>
                @endif
              </ul>
            </li>
          @endif

          @if (auth('user')->user()->has_permission('read-expenses') || auth('user')->user()->has_permission('read-expense-categories'))
            <li class="nav-item has-treeview {{request()->routeIs('dashboard.expenses.*') || request()->routeIs('dashboard.expense-categories.*') ? 'menu-open' : ''}}">
              <a href="#" class="nav-link {{request()->routeIs('dashboard.expenses.*') || request()->routeIs('dashboard.expense-categories.*') ? 'active' : ''}}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  {{ trans('admin.Expenses') }}
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @if (auth('user')->user()->has_permission('read-expenses'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.expenses.index')}}" class="nav-link {{request()->routeIs('dashboard.expenses.*')? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.expenses_list') }}</p>
                    </a>
                  </li>
                @endif
                @if (auth('user')->user()->has_permission('create-expenses'))
                <li class="nav-item">
                  <a href="{{route('dashboard.expenses.create')}}" class="nav-link {{request()->routeIs('dashboard.expenses.create')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.add_expense') }}</p>
                  </a>
                </li>
              @endif
                @if (auth('user')->user()->has_permission('read-expense-categories'))
                  <li class="nav-item">
                    <a href="{{route('dashboard.expense-categories.index')}}" class="nav-link {{request()->routeIs('dashboard.expense-categories.*')? 'active':''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ trans('admin.expense_categories') }}</p>
                    </a>
                  </li>
                @endif
              </ul>
            </li>
          @endif
          @if (auth('user')->user()->has_permission('read-stock-transfers') || auth('user')->user()->has_permission('create-stock-transfers'))
          <li class="nav-item has-treeview {{request()->routeIs('dashboard.stock-transfers.*') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{request()->routeIs('dashboard.stock-transfers.*') ? 'active' : ''}}">
              <i class="nav-icon fas fa-exchange-alt"></i>
              <p>
                {{ trans('admin.stock_transfers') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if (auth('user')->user()->has_permission('read-stock-transfers'))
                <li class="nav-item">
                  <a href="{{route('dashboard.stock-transfers.index')}}" class="nav-link {{request()->routeIs('dashboard.stock-transfers.index')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.stock_transfer_list') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('create-stock-transfers'))
                <li class="nav-item">
                  <a href="{{route('dashboard.stock-transfers.create')}}" class="nav-link {{request()->routeIs('dashboard.stock-transfers.create')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.add_stock_transfer') }}</p>
                  </a>
                </li>
              @endif
            </ul>
          </li>
          @endif
          @if (auth('user')->user()->has_permission('read-spoiled-stock') || auth('user')->user()->has_permission('create-spoiled-stock'))
          <li class="nav-item has-treeview {{request()->routeIs('dashboard.spoiled-stock.*') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{request()->routeIs('dashboard.spoiled-stock.*') ? 'active' : ''}}">
              <i class="nav-icon fas fa-exchange-alt"></i>
              <p>
                {{ trans('admin.spoiled_stock') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if (auth('user')->user()->has_permission('read-spoiled-stock'))
                <li class="nav-item">
                  <a href="{{route('dashboard.spoiled-stock.index')}}" class="nav-link {{request()->routeIs('dashboard.spoiled-stock.index')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.spoiled_stock_list') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('create-spoiled-stock'))
                <li class="nav-item">
                  <a href="{{route('dashboard.spoiled-stock.create')}}" class="nav-link {{request()->routeIs('dashboard.spoiled-stock.create')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.add_spoiled_stock') }}</p>
                  </a>
                </li>
              @endif
            </ul>
          </li>
          @endif
          @if (auth('user')->user()->has_permission('read-change-in-price-report-reports'))
          <li class="nav-item has-treeview {{request()->routeIs('dashboard.reports.*') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{request()->routeIs('dashboard.reports.*') ? 'active' : ''}}">
              <i class="nav-icon fas fa-exchange-alt"></i>
              <p>
                {{ trans('admin.reports') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if (auth('user')->user()->has_permission('read-stock-report-reports'))
                <li class="nav-item">
                  <a href="{{route('dashboard.reports.stock')}}" class="nav-link {{request()->routeIs('dashboard.reports.stock')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.stock_report') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('read-transaction-sell-report-reports'))
                <li class="nav-item">
                  <a href="{{route('dashboard.reports.transaction.sell')}}" class="nav-link {{request()->routeIs('dashboard.reports.transaction.sell')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.transaction_sell_report') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('read-expenses-report-reports'))
                <li class="nav-item">
                  <a href="{{route('dashboard.reports.expenses')}}" class="nav-link {{request()->routeIs('dashboard.reports.expenses')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Expenses') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('read-popular-products-report-reports'))
                <li class="nav-item">
                  <a href="{{route('dashboard.reports.popular.products')}}" class="nav-link {{request()->routeIs('dashboard.reports.popular.products')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.popular_products') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('read-spoiled-products-report-reports'))
                <li class="nav-item">
                  <a href="{{route('dashboard.reports.spoiled.products')}}" class="nav-link {{request()->routeIs('dashboard.reports.spoiled.products')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.spoiled_products') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('read-dept-report-reports'))
                <li class="nav-item">
                  <a href="{{route('dashboard.reports.dept.report')}}" class="nav-link {{request()->routeIs('dashboard.reports.dept.report')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.dept_report') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('read-change-in-price-report-reports'))
                <li class="nav-item">
                  <a href="{{route('dashboard.reports.change.in.price.report')}}" class="nav-link {{request()->routeIs('dashboard.reports.change.in.price.report')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Change in Price Report') }}</p>
                  </a>
                </li>
              @endif
              @if (auth('user')->user()->has_permission('read-profit-and-loss-report-reports'))
                <li class="nav-item">
                  <a href="{{route('dashboard.reports.profit.and.loss.report')}}" class="nav-link {{request()->routeIs('dashboard.reports.profit.and.loss.report')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.profit_and_loss_report') }}</p>
                  </a>
                </li>
              @endif
            </ul>
          </li>
          @endif
          @if (auth('user')->user()->has_permission('read-accounts'))
          <li class="nav-item">
            <a href="{{route('dashboard.accounts.index')}}" class="nav-link {{request()->routeIs('dashboard.accounts.*')? 'active':''}}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                {{ trans('admin.accounts') }}
              </p>
            </a>
          </li>
        @endif
          @if (auth('user')->user()->has_permission('read-settings'))
            <li class="nav-item {{(request()->routeIs('dashboard.settings.site.*') || request()->routeIs('dashboard.settings.sales') || request()->routeIs('dashboard.settings.invoice'))? 'menu-open':''}}">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  {{ trans('admin.Settings') }}
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('dashboard.settings.site')}}" class="nav-link {{request()->routeIs('dashboard.settings.site')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Site Settings') }}</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('dashboard.settings.products')}}" class="nav-link {{request()->routeIs('dashboard.settings.products')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Products Settings') }}</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('dashboard.settings.contacts')}}" class="nav-link {{request()->routeIs('dashboard.settings.contacts')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Contacts Settings') }}</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('dashboard.settings.sales')}}" class="nav-link {{request()->routeIs('dashboard.settings.sales')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Sales Settings') }}</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('dashboard.settings.invoice')}}" class="nav-link {{request()->routeIs('dashboard.settings.invoice')? 'active':''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ trans('admin.Invoice Settings') }}</p>
                  </a>
                </li>
               
              </ul>
            </li>
          @endif
          @if (auth('user')->user()->has_permission('read-site-settings'))
          <li class="nav-item {{(request()->routeIs('dashboard.site-setting.*') || request()->routeIs('dashboard.site-setting') || request()->routeIs('dashboard.site-setting'))? 'menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                {{ trans('admin.site') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('dashboard.site-setting.edit')}}" class="nav-link {{request()->routeIs('dashboard.site-setting.edit')? 'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ trans('admin.site-settings') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('dashboard.site-setting.sliders')}}" class="nav-link {{request()->routeIs('dashboard.site-setting.sliders')? 'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ trans('admin.site-slider') }}</p>
                </a>
              </li>
              
             
            </ul>
          </li>
        @endif
          @if (auth('user')->user()->has_permission('read-activity-logs'))
            <li class="nav-item">
              <a href="{{route('dashboard.activity-log.index')}}" class="nav-link {{request()->routeIs('dashboard.activity-log.*')? 'active':''}}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  {{ trans('admin.activity-log') }}
                </p>
              </a>
            </li>
          @endif

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>