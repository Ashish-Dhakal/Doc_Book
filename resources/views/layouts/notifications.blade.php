<script>
    toastr.options = {
        "positionClass": "toast-bottom-right", 
        "timeOut": "7000", 
    };

    @if(session('success'))
        toastr.success('{{ session('success') }}', 'Success');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}', 'Error');
    @endif

    @if(session('warning'))
        toastr.warning('{{ session('warning') }}', 'Warning');
    @endif

    @if(session('info'))
        toastr.info('{{ session('info') }}', 'Info');
    @endif
</script>
