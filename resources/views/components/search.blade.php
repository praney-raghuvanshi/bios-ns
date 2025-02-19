<!-- resources/views/components/search.blade.php -->
<div class="bios-search-container">
    <form action="{{ route('search') }}" method="GET" class="bios-search-form">
        <div class="bios-search-icon" id="bios-search-icon" onclick="toggleSearchBox()">
            <i class="ti ti-search ti-md bios-icon-search"></i>
        </div>
        <input type="text" name="query" id="bios-search-input" class="bios-search-input form-control"
            placeholder="Search..." />
    </form>
</div>

<style>
    .bios-search-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .bios-search-icon {
        cursor: pointer;
    }

    .bios-search-input {
        display: none;
        width: 0;
        transition: width 0.3s ease, display 0.3s ease;
    }

    .bios-search-input.open {
        display: inline-block;
        width: 200px;
    }
</style>