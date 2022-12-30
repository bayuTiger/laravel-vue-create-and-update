@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">登録・更新</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('store') }}">
                            @csrf
                            {{-- 名前 --}}
                            <div class="col-md-6">
                                <input v-model="name" id="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror" name="text" required>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- メールアドレス --}}
                            <div class="col-md-6">
                                <input v-model="email" id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email" required>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- パスワード --}}
                            <div class="col-md-6">
                                <input v-model="password" id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('js')
    <script>
        const app = new Vue({
            el: '#app',
            data: () => {
                return {
                    name: '',
                    email: '',
                    password: '',
                }
            },
            mounted: function() {
                console.log('mounted');
            }
        });
    </script>
@endsection
